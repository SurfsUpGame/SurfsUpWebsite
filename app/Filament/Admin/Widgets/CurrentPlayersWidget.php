<?php

namespace App\Filament\Admin\Widgets;

use App\Services\SteamPlayerCountService;
use App\Models\PlayerCountHistory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CurrentPlayersWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    // Refresh every 5 minutes
    protected static ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        $steamService = new SteamPlayerCountService();
        $currentPlayers = $steamService->getCurrentPlayerCount();

        // Get peak and low directly from database
        $peakAndLow = PlayerCountHistory::getPeakAndLowForLast24Hours();
        $peak24h = $peakAndLow['peak'];
        $low24h = $peakAndLow['low'];

        // Get database history for trend calculation
        $dbHistory = PlayerCountHistory::getHistoryForLast24Hours();
        $trend = null;
        $trendColor = 'gray';

        if ($dbHistory->count() >= 10) {
            $recent = $dbHistory->sortByDesc('recorded_at')->take(5);
            $older = $dbHistory->sortByDesc('recorded_at')->skip(5)->take(5);

            if ($recent->isNotEmpty() && $older->isNotEmpty()) {
                $recentAvg = $recent->avg('player_count');
                $olderAvg = $older->avg('player_count');

                $percentChange = $olderAvg > 0 ? (($recentAvg - $olderAvg) / $olderAvg) * 100 : 0;

                if (abs($percentChange) > 5) {
                    $trend = ($percentChange > 0 ? '+' : '') . number_format($percentChange, 1) . '%';
                    $trendColor = $percentChange > 0 ? 'success' : 'danger';
                }
            }
        }

        $stats = [];

        // Current Players
        $currentStat = Stat::make('Current Players', $currentPlayers ?? 'N/A')
            ->description('Players online right now')
            ->icon('heroicon-o-users');

        if ($trend) {
            $currentStat->descriptionIcon($trendColor === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trendColor);
        }

        $stats[] = $currentStat;

        // 24h Peak - directly from database
        if ($peak24h !== null) {
            $peakRecord = PlayerCountHistory::where('player_count', $peak24h)
                ->where('recorded_at', '>=', now()->subHours(24))
                ->orderBy('recorded_at', 'desc')
                ->first();

            $peakTime = $peakRecord ? $peakRecord->recorded_at->format('H:i') : '';

            $stats[] = Stat::make('24h Peak', $peak24h)
                ->description("Highest at {$peakTime} today")
                ->descriptionIcon('heroicon-m-arrow-up')
                ->color('success')
                ->icon('heroicon-o-chart-bar');
        }

        // 24h Low - directly from database
        if ($low24h !== null) {
            $lowRecord = PlayerCountHistory::where('player_count', $low24h)
                ->where('recorded_at', '>=', now()->subHours(24))
                ->orderBy('recorded_at', 'desc')
                ->first();

            $lowTime = $lowRecord ? $lowRecord->recorded_at->format('H:i') : '';

            $stats[] = Stat::make('24h Low', $low24h)
                ->description("Lowest at {$lowTime} today")
                ->descriptionIcon('heroicon-m-arrow-down')
                ->color('warning')
                ->icon('heroicon-o-chart-bar');
        }

        return $stats;
    }
}

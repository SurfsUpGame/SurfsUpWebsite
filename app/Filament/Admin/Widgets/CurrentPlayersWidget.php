<?php

namespace App\Filament\Admin\Widgets;

use App\Services\SteamPlayerCountService;
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
        $playerData = $steamService->getPlayerCountWithHistory();

        $currentPlayers = $playerData['current'];
        $peak24h = $playerData['peak_24h'];
        $low24h = $playerData['low_24h'];

        // Calculate trend from history
        $history = $playerData['history'];
        $trend = null;
        $trendColor = 'gray';

        if (count($history) >= 2) {
            $recent = array_slice($history, -5); // Last 5 readings
            $older = array_slice($history, -10, -5); // Previous 5 readings

            if (!empty($recent) && !empty($older)) {
                $recentAvg = array_sum(array_column($recent, 'count')) / count($recent);
                $olderAvg = array_sum(array_column($older, 'count')) / count($older);

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

        // 24h Peak
        if ($peak24h !== null) {
            $stats[] = Stat::make('24h Peak', $peak24h)
                ->description('Highest in last 24 hours')
                ->descriptionIcon('heroicon-m-arrow-up')
                ->color('success')
                ->icon('heroicon-o-chart-bar');
        }

        // 24h Low
        if ($low24h !== null) {
            $stats[] = Stat::make('24h Low', $low24h)
                ->description('Lowest in last 24 hours')
                ->descriptionIcon('heroicon-m-arrow-down')
                ->color('warning')
                ->icon('heroicon-o-chart-bar');
        }

        return $stats;
    }
}

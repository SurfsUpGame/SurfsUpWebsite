<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecentLoginsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        $recentLogins = User::query()
            ->whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subHours(24))
            ->count();

        $totalUsers = User::count();

        $newUsers = User::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $percentage = $totalUsers > 0 ? round(($recentLogins / $totalUsers) * 100, 1) : 0;

        return [
            Stat::make('Recent Logins (24h)', $recentLogins)
                ->description("$percentage% of all users")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-user-group'),
            
            Stat::make('Total Users', $totalUsers)
                ->description('All registered users')
                ->color('primary')
                ->icon('heroicon-o-users'),
            
            Stat::make('New Users (7 days)', $newUsers)
                ->description('Recently registered')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning')
                ->icon('heroicon-o-user-plus'),
        ];
    }
}
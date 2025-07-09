<?php

namespace App\Filament\Admin\Widgets;

use App\Models\TaskVote;
use App\Models\SuggestionVote;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VoteStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        $taskVotes = TaskVote::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $suggestionVotes = SuggestionVote::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $totalVotes = $taskVotes + $suggestionVotes;

        // Calculate trend for task votes
        $taskVotesLastWeek = TaskVote::query()
            ->where('created_at', '>=', now()->subDays(14))
            ->where('created_at', '<', now()->subDays(7))
            ->count();

        $taskVoteTrend = null;
        $taskVoteTrendColor = 'gray';

        if ($taskVotesLastWeek > 0) {
            $taskVoteChange = (($taskVotes - $taskVotesLastWeek) / $taskVotesLastWeek) * 100;
            if (abs($taskVoteChange) > 10) {
                $taskVoteTrend = ($taskVoteChange > 0 ? '+' : '') . number_format($taskVoteChange, 1) . '%';
                $taskVoteTrendColor = $taskVoteChange > 0 ? 'success' : 'danger';
            }
        }

        // Calculate trend for suggestion votes
        $suggestionVotesLastWeek = SuggestionVote::query()
            ->where('created_at', '>=', now()->subDays(14))
            ->where('created_at', '<', now()->subDays(7))
            ->count();

        $suggestionVoteTrend = null;
        $suggestionVoteTrendColor = 'gray';

        if ($suggestionVotesLastWeek > 0) {
            $suggestionVoteChange = (($suggestionVotes - $suggestionVotesLastWeek) / $suggestionVotesLastWeek) * 100;
            if (abs($suggestionVoteChange) > 10) {
                $suggestionVoteTrend = ($suggestionVoteChange > 0 ? '+' : '') . number_format($suggestionVoteChange, 1) . '%';
                $suggestionVoteTrendColor = $suggestionVoteChange > 0 ? 'success' : 'danger';
            }
        }

        $stats = [];

        // Total votes stat
        $totalStat = Stat::make('Total Votes (7 days)', $totalVotes)
            ->description('Tasks + Suggestions')
            ->color('primary')
            ->icon('heroicon-o-hand-thumb-up');

        $stats[] = $totalStat;

        // Task votes stat
        $taskStat = Stat::make('Task Votes (7 days)', $taskVotes)
            ->description('Votes on roadmap tasks')
            ->color('success')
            ->icon('heroicon-o-check-badge');

        if ($taskVoteTrend) {
            $taskStat->description($taskVoteTrend . ' from last week')
                ->descriptionIcon($taskVoteTrendColor === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($taskVoteTrendColor);
        }

        $stats[] = $taskStat;

        // Suggestion votes stat
        $suggestionStat = Stat::make('Suggestion Votes (7 days)', $suggestionVotes)
            ->description('Votes on user suggestions')
            ->color('warning')
            ->icon('heroicon-o-light-bulb');

        if ($suggestionVoteTrend) {
            $suggestionStat->description($suggestionVoteTrend . ' from last week')
                ->descriptionIcon($suggestionVoteTrendColor === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($suggestionVoteTrendColor);
        }

        $stats[] = $suggestionStat;

        return $stats;
    }
}

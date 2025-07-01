<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\SteamLeaderboardService;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller
{
    public function show($steamId)
    {
        // Find user by Steam ID or create a temporary user object
        $user = User::where('steam_id', $steamId)->first();

        if (!$user) {
            // If user doesn't exist in our database, we'll still show the leaderboard
            // but create a temporary user object with just the Steam ID
            $user = new User(['steam_id' => $steamId]);
        }

        // Try to get cached complete rankings first
        $completeRankingsCacheKey = "steam_user_complete_rankings_{$steamId}";
        $rankings = Cache::get($completeRankingsCacheKey);

        if (!$rankings) {
            // Fallback to manual fetching if no cache
            $leaderboardService = new SteamLeaderboardService();
            $leaderboards = $leaderboardService->getLeaderboards();
            $rankings = [];

            // For each leaderboard, get the user's rank
            foreach ($leaderboards as $leaderboard) {
                if (isset($leaderboard['id'])) {
                    $rankData = $leaderboardService->getUserRank($steamId, $leaderboard['id']);

                    if ($rankData) {
                        $leaderboard['rank_data'] = $rankData;
                    }

                    $rankings[] = $leaderboard;
                }
            }
        }

        // Filter to only show maps with scores
        $rankings = array_filter($rankings, function($ranking) {
            return isset($ranking['rank_data']);
        });

        // Sort by rank (lowest rank number first)
        usort($rankings, function($a, $b) {
            $aRank = $a['rank_data']['rank'] ?? PHP_INT_MAX;
            $bRank = $b['rank_data']['rank'] ?? PHP_INT_MAX;
            return $aRank <=> $bRank;
        });

        return view('leaderboard.show', [
            'user' => $user,
            'rankings' => $rankings,
            'steamId' => $steamId
        ]);
    }
}

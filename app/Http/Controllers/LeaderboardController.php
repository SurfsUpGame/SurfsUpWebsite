<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\SteamLeaderboardService;

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

        // Get leaderboard data for this Steam ID
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

        // Filter to only show maps with scores
        $rankings = array_filter($rankings, function($ranking) {
            return isset($ranking['rank_data']);
        });

        return view('leaderboard.show', [
            'user' => $user,
            'rankings' => $rankings,
            'steamId' => $steamId
        ]);
    }
}
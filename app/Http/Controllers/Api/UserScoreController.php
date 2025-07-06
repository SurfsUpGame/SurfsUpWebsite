<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SteamLeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserScoreController extends Controller
{
    private SteamLeaderboardService $steamService;

    public function __construct(SteamLeaderboardService $steamService)
    {
        $this->steamService = $steamService;
    }

    /**
     * Get user's best score across all leaderboards
     */
    public function getUserScore(Request $request, string $steamId): JsonResponse
    {
        $validator = Validator::make(['steam_id' => $steamId], [
            'steam_id' => 'required|string|regex:/^[0-9]{17}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid Steam ID format',
                'message' => 'Steam ID must be a 17-digit number'
            ], 400);
        }

        try {
            // Get all leaderboards
            $leaderboards = $this->steamService->getLeaderboards();
            
            if (empty($leaderboards)) {
                return response()->json([
                    'error' => 'No leaderboards available',
                    'message' => 'Unable to fetch leaderboards from Steam'
                ], 503);
            }

            $userScores = [];
            $bestScore = null;

            // Get user's rank on each leaderboard
            foreach ($leaderboards as $leaderboard) {
                $userRank = $this->steamService->getUserRank($steamId, $leaderboard['id']);
                
                if ($userRank && isset($userRank['score'])) {
                    $scoreData = [
                        'leaderboard_id' => $leaderboard['id'],
                        'leaderboard_name' => $leaderboard['display_name'],
                        'score' => $userRank['score'],
                        'rank' => $userRank['rank'],
                        'total_entries' => $userRank['total_entries'],
                        'percentile' => $userRank['percentile']
                    ];
                    
                    $userScores[] = $scoreData;
                    
                    // Determine best score based on rank (lower rank = better)
                    if ($bestScore === null || $userRank['rank'] < $bestScore['rank']) {
                        $bestScore = $scoreData;
                    }
                }
            }

            if (empty($userScores)) {
                return response()->json([
                    'steam_id' => $steamId,
                    'message' => 'No scores found for this user',
                    'best_score' => null,
                    'all_scores' => []
                ], 404);
            }

            return response()->json([
                'steam_id' => $steamId,
                'best_score' => $bestScore,
                'total_leaderboards' => count($userScores),
                'all_scores' => $userScores
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user score', [
                'steam_id' => $steamId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Unable to fetch user scores at this time'
            ], 500);
        }
    }

    /**
     * Get all user's scores across all leaderboards
     */
    public function getAllUserScores(Request $request, string $steamId): JsonResponse
    {
        $validator = Validator::make(['steam_id' => $steamId], [
            'steam_id' => 'required|string|regex:/^[0-9]{17}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid Steam ID format',
                'message' => 'Steam ID must be a 17-digit number'
            ], 400);
        }

        try {
            // Get all leaderboards
            $leaderboards = $this->steamService->getLeaderboards();
            
            if (empty($leaderboards)) {
                return response()->json([
                    'error' => 'No leaderboards available',
                    'message' => 'Unable to fetch leaderboards from Steam'
                ], 503);
            }

            $userScores = [];

            // Get user's rank on each leaderboard
            foreach ($leaderboards as $leaderboard) {
                $userRank = $this->steamService->getUserRank($steamId, $leaderboard['id']);
                
                $scoreData = [
                    'leaderboard_id' => $leaderboard['id'],
                    'leaderboard_name' => $leaderboard['display_name'],
                    'sort_method' => $leaderboard['sort_method'],
                    'display_type' => $leaderboard['display_type'],
                    'entry_count' => $leaderboard['entry_count']
                ];

                if ($userRank && isset($userRank['score'])) {
                    $scoreData = array_merge($scoreData, [
                        'score' => $userRank['score'],
                        'rank' => $userRank['rank'],
                        'total_entries' => $userRank['total_entries'],
                        'percentile' => $userRank['percentile'],
                        'has_score' => true
                    ]);
                } else {
                    $scoreData = array_merge($scoreData, [
                        'score' => null,
                        'rank' => null,
                        'total_entries' => $leaderboard['entry_count'],
                        'percentile' => null,
                        'has_score' => false
                    ]);
                }
                
                $userScores[] = $scoreData;
            }

            return response()->json([
                'steam_id' => $steamId,
                'total_leaderboards' => count($leaderboards),
                'scores_found' => count(array_filter($userScores, fn($s) => $s['has_score'])),
                'scores' => $userScores
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching all user scores', [
                'steam_id' => $steamId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Unable to fetch user scores at this time'
            ], 500);
        }
    }

    /**
     * Get user's rank on a specific leaderboard
     */
    public function getUserLeaderboardRank(Request $request, string $steamId, string $leaderboardId): JsonResponse
    {
        $validator = Validator::make([
            'steam_id' => $steamId,
            'leaderboard_id' => $leaderboardId
        ], [
            'steam_id' => 'required|string|regex:/^[0-9]{17}$/',
            'leaderboard_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid parameters',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            // Get leaderboard info
            $leaderboards = $this->steamService->getLeaderboards();
            $leaderboard = collect($leaderboards)->firstWhere('id', $leaderboardId);
            
            if (!$leaderboard) {
                return response()->json([
                    'error' => 'Leaderboard not found',
                    'message' => 'The specified leaderboard does not exist'
                ], 404);
            }

            // Get user's rank
            $userRank = $this->steamService->getUserRank($steamId, $leaderboardId);
            
            if (!$userRank || !isset($userRank['score'])) {
                return response()->json([
                    'steam_id' => $steamId,
                    'leaderboard_id' => $leaderboardId,
                    'leaderboard_name' => $leaderboard['display_name'],
                    'message' => 'No score found for this user on this leaderboard',
                    'score' => null
                ], 404);
            }

            // Get entries around user for context
            $entriesAroundUser = $this->steamService->getLeaderboardEntriesAroundUser($leaderboardId, $steamId, 10);

            return response()->json([
                'steam_id' => $steamId,
                'leaderboard_id' => $leaderboardId,
                'leaderboard_name' => $leaderboard['display_name'],
                'score' => $userRank['score'],
                'rank' => $userRank['rank'],
                'total_entries' => $userRank['total_entries'],
                'percentile' => $userRank['percentile'],
                'entries_around_user' => $entriesAroundUser
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user leaderboard rank', [
                'steam_id' => $steamId,
                'leaderboard_id' => $leaderboardId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Unable to fetch user leaderboard rank at this time'
            ], 500);
        }
    }
}
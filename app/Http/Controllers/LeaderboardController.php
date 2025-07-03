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
            // If user doesn't exist in our database, fetch their Steam profile
            $steamProfile = $this->fetchSteamProfile($steamId);

            // Create a permanent user record with Steam profile data
            $user = User::create([
                'steam_id' => $steamId,
                'name' => $steamProfile['name'] ?? 'Player',
                'avatar' => $steamProfile['avatar'] ?? null,
                'email' => $steamId . '@steamauth.local',
                'password' => bcrypt(str()->random(32)),
            ]);
        }

        return view('leaderboard.show', [
            'user' => $user,
            'steamId' => $steamId
        ]);
    }

    private function fetchSteamProfile($steamId)
    {
        // Check cache first
        $cacheKey = "steam_profile_{$steamId}";
        $cachedProfile = Cache::get($cacheKey);

        if ($cachedProfile !== null) {
            return $cachedProfile;
        }

        $apiKey = config('steam-auth.api_keys')[0] ?? null;

        if (!$apiKey) {
            $fallback = ['name' => 'Player', 'avatar' => null];
            Cache::put($cacheKey, $fallback, 3600); // Cache for 1 hour
            return $fallback;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/', [
                'key' => $apiKey,
                'steamids' => $steamId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['response']['players'][0])) {
                    $player = $data['response']['players'][0];
                    $profile = [
                        'name' => $player['personaname'] ?? 'Player',
                        'avatar' => $player['avatarfull'] ?? $player['avatarmedium'] ?? $player['avatar'] ?? null
                    ];

                    // Cache the profile for 1 hour
                    Cache::put($cacheKey, $profile, 3600);
                    return $profile;
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to fetch Steam profile', [
                'steam_id' => $steamId,
                'error' => $e->getMessage()
            ]);
        }

        $fallback = ['name' => 'Player', 'avatar' => null];
        Cache::put($cacheKey, $fallback, 3600); // Cache even failures to avoid repeated API calls
        return $fallback;
    }
}

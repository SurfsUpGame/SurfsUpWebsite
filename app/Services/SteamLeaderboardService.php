<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SteamLeaderboardService
{
    const STEAM_API_URL = 'https://api.steampowered.com/ISteamLeaderboards/';
    const STEAM_PARTNER_API_URL = 'https://partner.steam-api.com/ISteamLeaderboards/GetLeaderboardsForGame/v2/';

    private ?string $apiKey;
    private ?string $publisherApiKey;
    private string $appId;

    public function __construct()
    {
        $this->apiKey = config('steam-auth.api_keys')[0] ?? null;
        $this->publisherApiKey = env('STEAM_PUBLISHER_API_KEY');
        $this->appId = env('STEAM_APP_ID', '3454830');
    }

    /**
     * Get all leaderboards for the app
     */
    public function getLeaderboards(): array
    {
        $cacheKey = "steam_leaderboards_{$this->appId}";

        return Cache::remember($cacheKey, 3600, function () {
            if (!$this->publisherApiKey) {
                Log::warning('No Steam Publisher API key configured for leaderboards');
                return [];
            }

            try {
                $response = Http::timeout(10)->get(self::STEAM_PARTNER_API_URL, [
                    'key' => $this->publisherApiKey,
                    'appid' => $this->appId,
                ]);

//                Log::info('Steam leaderboards API response', [
//                    'status' => $response->status(),
//                    'body' => $response->body()
//                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['response']['leaderboards'])) {
                        $leaderboards = [];
                        foreach ($data['response']['leaderboards'] as $leaderboard) {
                            $leaderboards[] = [
                                'id' => $leaderboard['id'] ?? null,
                                'name' => $leaderboard['name'] ?? 'Unknown',
                                'display_name' => $leaderboard['display_name'] ?? $leaderboard['name'] ?? 'Unknown',
                                'sort_method' => $leaderboard['sort_method'] ?? 'descending',
                                'display_type' => $leaderboard['display_type'] ?? 'numeric',
                                'entry_count' => $leaderboard['entry_count'] ?? 0,
                            ];
                        }

                        // Log::info('Successfully fetched Steam leaderboards', ['count' => count($leaderboards)]);
                        return $leaderboards;
                    }
                }

                Log::warning('Failed to fetch Steam leaderboards', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

            } catch (\Exception $e) {
                Log::error('Error fetching Steam leaderboards', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // No fallback - return empty array if real data unavailable
            return [];
        });
    }

    /**
     * Get user's rank on a specific leaderboard
     */
    public function getUserRank(string $steamId, string $leaderboardId): ?array
    {
        $cacheKey = "steam_leaderboard_rank_{$steamId}_{$leaderboardId}";

        return Cache::remember($cacheKey, 900, function () use ($steamId, $leaderboardId) {
            if (!$this->publisherApiKey) {
                Log::warning('No Steam Publisher API key configured for user rank');
                return null;
            }

            try {
                $response = Http::timeout(10)->get('https://partner.steam-api.com/ISteamLeaderboards/GetLeaderboardEntries/v1/', [
                    'key' => $this->publisherApiKey,
                    'appid' => $this->appId,
                    'rangestart' => 0,
                    'rangeend' => 0,
                    'leaderboardid' => $leaderboardId,
                    'steamid' => $steamId,
                    'datarequest' => 'RequestAroundUser',
                ]);

//                Log::info('Steam user rank API response', [
//                    'leaderboard_id' => $leaderboardId,
//                    'steam_id' => $steamId,
//                    'status' => $response->status(),
//                    'body' => $response->body()
//                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['leaderboardEntryInformation']['leaderboardEntries']) && count($data['leaderboardEntryInformation']['leaderboardEntries']) > 0) {
                        $entry = $data['leaderboardEntryInformation']['leaderboardEntries'][0];
                        $totalEntries = $data['leaderboardEntryInformation']['totalLeaderBoardEntryCount'] ?? 0;

                        $rank = $entry['rank'] ?? 0;
                        $percentile = $totalEntries > 0 ? round((($totalEntries - $rank) / $totalEntries) * 100, 2) : 0;

                        return [
                            'rank' => $rank,
                            'score' => $entry['score'] ?? 0,
                            'total_entries' => $totalEntries,
                            'percentile' => $percentile,
                            'details' => [
                                'time' => isset($entry['ugcid']) ? '-' : '-',
                                'date' => now()->format('Y-m-d'),
                            ],
                        ];
                    }
                }

                Log::warning('Failed to fetch Steam user rank', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

            } catch (\Exception $e) {
                Log::error('Error fetching Steam user rank', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // No fallback - return null if real data unavailable
            return null;
        });
    }

    /**
     * Get top entries for a leaderboard
     */
    public function getLeaderboardEntries(string $leaderboardId, int $limit = 10): array
    {
        $cacheKey = "steam_leaderboard_entries_{$leaderboardId}_{$limit}";

        return Cache::remember($cacheKey, 900, function () use ($leaderboardId, $limit) {
            if (!$this->publisherApiKey) {
                Log::warning('No Steam Publisher API key configured for leaderboard entries');
                return [];
            }

            try {
                $response = Http::timeout(10)->get('https://partner.steam-api.com/ISteamLeaderboards/GetLeaderboardEntries/v1/', [
                    'key' => $this->publisherApiKey,
                    'appid' => $this->appId,
                    'rangestart' => 0,
                    'rangeend' => $limit,
                    'leaderboardid' => $leaderboardId,
                    'datarequest' => 'RequestGlobal',
                ]);

//                Log::info('Steam leaderboard entries API response', [
//                    'leaderboard_id' => $leaderboardId,
//                    'status' => $response->status(),
//                    'body' => $response->body()
//                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['leaderboardEntryInformation']['leaderboardEntries'])) {
                        $entries = [];
                        foreach ($data['leaderboardEntryInformation']['leaderboardEntries'] as $entry) {
                            // Get Steam user info for persona name
                            $personaName = $this->getSteamUserName($entry['steamID'] ?? '');

                            $entries[] = [
                                'rank' => $entry['rank'] ?? 0,
                                'steam_id' => $entry['steamID'] ?? '',
                                'score' => $entry['score'] ?? 0,
                                'persona_name' => $personaName,
                                'details' => [
                                    'time' => '-',
                                ],
                            ];
                        }

                        Log::info('Successfully fetched Steam leaderboard entries', ['count' => count($entries)]);
                        return $entries;
                    }
                }

                Log::warning('Failed to fetch Steam leaderboard entries', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

            } catch (\Exception $e) {
                Log::error('Error fetching Steam leaderboard entries', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // No fallback - return empty array if real data unavailable
            return [];
        });
    }

    /**
     * Get leaderboard entries around a specific user
     */
    public function getLeaderboardEntriesAroundUser(string $leaderboardId, string $steamId, int $limit = 10): array
    {
        $cacheKey = "steam_leaderboard_around_{$leaderboardId}_{$steamId}_{$limit}";

        return Cache::remember($cacheKey, 900, function () use ($leaderboardId, $steamId, $limit) {
            if (!$this->publisherApiKey) {
                Log::warning('No Steam Publisher API key configured for leaderboard entries around user');
                return [];
            }

            try {
                $response = Http::timeout(10)->get('https://partner.steam-api.com/ISteamLeaderboards/GetLeaderboardEntries/v1/', [
                    'key' => $this->publisherApiKey,
                    'appid' => $this->appId,
                    'rangestart' => -($limit / 2),
                    'rangeend' => ($limit / 2),
                    'leaderboardid' => $leaderboardId,
                    'steamid' => $steamId,
                    'datarequest' => 'RequestAroundUser',
                ]);

                Log::info('Steam leaderboard entries around user API response', [
                    'leaderboard_id' => $leaderboardId,
                    'steam_id' => $steamId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['leaderboardEntryInformation']['leaderboardEntries'])) {
                        $entries = [];
                        foreach ($data['leaderboardEntryInformation']['leaderboardEntries'] as $entry) {
                            // Get Steam user info for persona name
                            $personaName = $this->getSteamUserName($entry['steamID'] ?? '');

                            $entries[] = [
                                'rank' => $entry['rank'] ?? 0,
                                'steam_id' => $entry['steamID'] ?? '',
                                'score' => $entry['score'] ?? 0,
                                'persona_name' => $personaName,
                                'is_current_user' => ($entry['steamID'] ?? '') === $steamId,
                                'details' => [
                                    'time' => '-',
                                ],
                            ];
                        }

                        Log::info('Successfully fetched Steam leaderboard entries around user', ['count' => count($entries)]);
                        return $entries;
                    }
                }

                Log::warning('Failed to fetch Steam leaderboard entries around user', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

            } catch (\Exception $e) {
                Log::error('Error fetching Steam leaderboard entries around user', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // No fallback - return empty array if real data unavailable
            return [];
        });
    }

    /**
     * Get Steam user's persona name from Steam ID
     */
    private function getSteamUserName(string $steamId): string
    {
        if (empty($steamId) || !$this->apiKey) {
            return 'Unknown Player';
        }

        $cacheKey = "steam_user_name_{$steamId}";

        return Cache::remember($cacheKey, 3600, function () use ($steamId) {
            try {
                $response = Http::timeout(5)->get('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/', [
                    'key' => $this->apiKey,
                    'steamids' => $steamId,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['response']['players'][0]['personaname'])) {
                        return $data['response']['players'][0]['personaname'];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch Steam user name', [
                    'steam_id' => $steamId,
                    'error' => $e->getMessage()
                ]);
            }

            return 'Player ' . substr($steamId, -4);
        });
    }


}

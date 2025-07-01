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

        // Try to pull from cache first (retrieve and remove)
        $cachedLeaderboards = Cache::pull($cacheKey);
        if ($cachedLeaderboards !== null) {
            return $cachedLeaderboards;
        }

        // If not in cache, fetch and cache the result
        return Cache::remember($cacheKey, 7200, function () {
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

        // Try to pull from cache first (retrieve and remove)
        $cachedRank = Cache::pull($cacheKey);
        if ($cachedRank !== null) {
            return $cachedRank;
        }

        // If not in cache, fetch and cache the result
        return Cache::remember($cacheKey, 1800, function () use ($steamId, $leaderboardId) {
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

        return Cache::remember($cacheKey, 1800, function () use ($leaderboardId, $limit) {
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

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['leaderboardEntryInformation']['leaderboardEntries'])) {
                        $entries = [];
                        $steamIds = [];

                        // Collect all Steam IDs first
                        foreach ($data['leaderboardEntryInformation']['leaderboardEntries'] as $entry) {
                            if (!empty($entry['steamID'])) {
                                $steamIds[] = $entry['steamID'];
                            }
                        }

                        // Batch fetch all user names at once
                        $userNames = $this->getSteamUserNamesBatch($steamIds);

                        // Build entries with fetched names
                        foreach ($data['leaderboardEntryInformation']['leaderboardEntries'] as $entry) {
                            $steamId = $entry['steamID'] ?? '';
                            $entries[] = [
                                'rank' => $entry['rank'] ?? 0,
                                'steam_id' => $steamId,
                                'score' => $entry['score'] ?? 0,
                                'persona_name' => $userNames[$steamId] ?? 'Player ' . substr($steamId, -4),
                                'details' => [
                                    'time' => '-',
                                ],
                            ];
                        }

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

            return [];
        });
    }

    /**
     * Get leaderboard entries around a specific user
     */
    public function getLeaderboardEntriesAroundUser(string $leaderboardId, string $steamId, int $limit = 10): array
    {
        $cacheKey = "steam_leaderboard_around_{$leaderboardId}_{$steamId}_{$limit}";

        return Cache::remember($cacheKey, 1800, function () use ($leaderboardId, $steamId, $limit) {
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

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['leaderboardEntryInformation']['leaderboardEntries'])) {
                        $entries = [];
                        $steamIds = [];

                        // Collect all Steam IDs first
                        foreach ($data['leaderboardEntryInformation']['leaderboardEntries'] as $entry) {
                            if (!empty($entry['steamID'])) {
                                $steamIds[] = $entry['steamID'];
                            }
                        }

                        // Batch fetch all user names at once
                        $userNames = $this->getSteamUserNamesBatch($steamIds);

                        // Build entries with fetched names
                        foreach ($data['leaderboardEntryInformation']['leaderboardEntries'] as $entry) {
                            $entryId = $entry['steamID'] ?? '';
                            $entries[] = [
                                'rank' => $entry['rank'] ?? 0,
                                'steam_id' => $entryId,
                                'score' => $entry['score'] ?? 0,
                                'persona_name' => $userNames[$entryId] ?? 'Player ' . substr($entryId, -4),
                                'is_current_user' => $entryId === $steamId,
                                'details' => [
                                    'time' => '-',
                                ],
                            ];
                        }

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

            return [];
        });
    }

    /**
     * Get Steam users' persona names from Steam IDs in batch
     */
    private function getSteamUserNamesBatch(array $steamIds): array
    {
        if (empty($steamIds) || !$this->apiKey) {
            return [];
        }

        // Remove duplicates
        $steamIds = array_unique($steamIds);
        $results = [];

        // Check cache for each ID first
        $uncachedIds = [];
        foreach ($steamIds as $steamId) {
            $cacheKey = "steam_user_name_$steamId";
            $cachedName = Cache::get($cacheKey);
            if ($cachedName !== null) {
                $results[$steamId] = $cachedName;
            } else {
                $uncachedIds[] = $steamId;
            }
        }

        // If all are cached, return early
        if (empty($uncachedIds)) {
            return $results;
        }

        // Steam API allows up to 100 Steam IDs per request
        $chunks = array_chunk($uncachedIds, 100);

        foreach ($chunks as $chunk) {
            try {
                $response = Http::timeout(5)->get('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/', [
                    'key' => $this->apiKey,
                    'steamids' => implode(',', $chunk),
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['response']['players'])) {
                        foreach ($data['response']['players'] as $player) {
                            $steamId = $player['steamid'] ?? '';
                            $personaName = $player['personaname'] ?? 'Player ' . substr($steamId, -4);

                            // Cache individual name
                            $cacheKey = "steam_user_name_$steamId";
                            Cache::put($cacheKey, $personaName, 3600);

                            $results[$steamId] = $personaName;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to batch fetch Steam user names', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fill in any missing names with default
        foreach ($uncachedIds as $steamId) {
            if (!isset($results[$steamId])) {
                $results[$steamId] = 'Player ' . substr($steamId, -4);
            }
        }

        return $results;
    }

}

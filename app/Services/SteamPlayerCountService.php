<?php

namespace App\Services;

use App\Models\PlayerCountHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SteamPlayerCountService
{
    private string $steamApiKey;
    private string $appId;
    private string $baseUrl = 'https://partner.steam-api.com/ISteamUserStats/GetNumberOfCurrentPlayers/v1/';

    public function __construct()
    {
        $this->steamApiKey = config('services.steam.api_key');
        $this->appId = config('services.steam.app_id', '3454830');
    }

    public function getCurrentPlayerCount(): ?int
    {
        return Cache::remember('steam_current_players', 300, function () {
            try {
                $response = Http::timeout(10)->get($this->baseUrl, [
                    'key' => $this->steamApiKey,
                    'appid' => $this->appId,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['response']['player_count'])) {
                        return (int) $data['response']['player_count'];
                    }
                }

                Log::warning('Steam API response unsuccessful', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('Error fetching Steam player count', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return null;
            }
        });
    }

    public function getCurrentPlayerCountAndRecord(): ?int
    {
        $currentCount = $this->getCurrentPlayerCount();
        
        // Record to database if we have a valid count
        if ($currentCount !== null) {
            $this->recordPlayerCount($currentCount);
        }
        
        return $currentCount;
    }
    
    public function recordPlayerCount(int $playerCount): void
    {
        // Check if we already have a record for this minute to avoid duplicates
        $now = now();
        $existingRecord = PlayerCountHistory::where('app_id', $this->appId)
            ->where('recorded_at', '>=', $now->startOfMinute())
            ->where('recorded_at', '<=', $now->endOfMinute())
            ->first();
            
        if (!$existingRecord) {
            PlayerCountHistory::recordPlayerCount($playerCount, $this->appId);
        }
    }
    
    public function getPlayerCountWithHistory(): array
    {
        $currentCount = $this->getCurrentPlayerCount();
        
        // Get database historical data (last 24 hours)
        $history = PlayerCountHistory::getHistoryForLast24Hours($this->appId);
        $peakAndLow = PlayerCountHistory::getPeakAndLowForLast24Hours($this->appId);
        
        // Convert database records to array format for compatibility
        $historyArray = $history->map(function ($record) {
            return [
                'timestamp' => $record->recorded_at->toISOString(),
                'count' => $record->player_count,
                'hour' => $record->recorded_at->format('H:i')
            ];
        })->toArray();
        
        return [
            'current' => $currentCount,
            'history' => $historyArray,
            'peak_24h' => $peakAndLow['peak'] ?? $currentCount,
            'low_24h' => $peakAndLow['low'] ?? $currentCount,
        ];
    }

    public function getChartData(): array
    {
        $chartData = PlayerCountHistory::getChartDataForLast72Hours($this->appId);
        
        // If we don't have enough real data, supplement with sample data for demo
        if (count(array_filter($chartData, fn($entry) => $entry['count'] > 0)) < 18) {
            $currentCount = $this->getCurrentPlayerCount() ?? 3;
            
            // Fill in missing hours with sample data
            foreach ($chartData as &$entry) {
                if ($entry['count'] == 0) {
                    $time = \Carbon\Carbon::parse($entry['timestamp']);
                    $timeOfDay = (int)$time->format('H');
                    
                    // Generate sample data with realistic variation
                    if ($timeOfDay >= 18 || $timeOfDay <= 2) {
                        $modifier = rand(0, 3); // Peak hours
                    } elseif ($timeOfDay >= 6 && $timeOfDay <= 12) {
                        $modifier = rand(-2, 1); // Morning hours
                    } else {
                        $modifier = rand(-1, 2); // Afternoon
                    }
                    
                    $entry['count'] = max(0, $currentCount + $modifier);
                }
            }
        }
        
        return $chartData;
    }
}
<?php

namespace App\Livewire;

use App\Services\SteamLeaderboardService;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SteamLeaderboard extends Component
{
    public $rankings = [];
    public $loading = true;
    public $userScoresLoading = [];
    public $selectedLeaderboard = null;
    public $selectedLeaderboardName = null;
    public $topEntries = [];
    public $aroundMeEntries = [];
    public $modalLoading = false;
    public $showModal = false;
    public $modalType = null;
    public $showOnlyWithScores = true;
    public $shareModalVisible = false;
    public $shareUrl = '';
    public $imageModalVisible = false;
    public $selectedImageUrl = '';
    public $selectedImageName = '';

    protected $leaderboardService;

    public function mount() : void
    {
        $this->loadUserRankings();
    }

    public function loadUserRankings(): void
    {
        $this->loading = true;
        $this->userScoresLoading = [];

        if (Auth::check() && Auth::user()->steam_id) {
            $steamId = Auth::user()->steam_id;
            $service = new SteamLeaderboardService();

            // Check for cached complete rankings first
            $completeRankingsCacheKey = "steam_user_complete_rankings_{$steamId}";
            $cachedCompleteRankings = Cache::pull($completeRankingsCacheKey);

            if ($cachedCompleteRankings) {
                // Use complete cached rankings
                $this->rankings = $cachedCompleteRankings;
                $this->userScoresLoading = array_fill_keys(array_column($this->rankings, 'id'), false);
                $this->loading = false;
                return;
            }

            // Get all leaderboards
            $leaderboards = $service->getLeaderboards();
            $this->rankings = [];

            if (empty($leaderboards)) {
                $this->loading = false;
                return;
            }

            // Batch check cache for all leaderboard ranks
            $cacheKeys = [];
            $leaderboardMap = [];

            foreach ($leaderboards as $index => $leaderboard) {
                if (isset($leaderboard['id'])) {
                    $cacheKey = "steam_leaderboard_rank_{$steamId}_{$leaderboard['id']}";
                    $cacheKeys[] = $cacheKey;
                    $leaderboardMap[$cacheKey] = ['leaderboard' => $leaderboard, 'index' => $index];
                }
            }

            // Batch get from cache
            $cachedRanks = Cache::many($cacheKeys);
            $idsToLoad = [];

            // Process results
            foreach ($leaderboards as $index => $leaderboard) {
                if (isset($leaderboard['id'])) {
                    $cacheKey = "steam_leaderboard_rank_{$steamId}_{$leaderboard['id']}";

                    if (isset($cachedRanks[$cacheKey]) && $cachedRanks[$cacheKey] !== null) {
                        // Use cached data
                        $leaderboard['rank_data'] = $cachedRanks[$cacheKey];
                        $this->userScoresLoading[$leaderboard['id']] = false;
                    } else {
                        // Mark for loading
                        $this->userScoresLoading[$leaderboard['id']] = true;
                        $idsToLoad[] = ['id' => $leaderboard['id'], 'index' => count($this->rankings)];
                    }

                    $this->rankings[] = $leaderboard;
                }
            }

            // Show the leaderboards immediately with loading states
            $this->loading = false;

            // If there are scores to load, load them
            if (!empty($idsToLoad)) {
                $this->dispatch('showUserScoreLoading');
                $this->batchLoadUserScores($idsToLoad);
            } else {
                // Cache complete rankings if all data is available
                $this->cacheCompleteRankings($steamId);
            }
        } else {
            $this->loading = false;
        }
    }

    public function batchLoadUserScores($idsToLoad): void
    {
        if (!Auth::check() || !Auth::user()->steam_id) {
            return;
        }

        $service = new SteamLeaderboardService();
        $steamId = Auth::user()->steam_id;

        // Process in small batches to avoid timeouts
        $chunks = array_chunk($idsToLoad, 5);
        $hasNewData = false;

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $rankData = $service->getUserRank($steamId, $item['id']);
                if ($rankData) {
                    $this->rankings[$item['index']]['rank_data'] = $rankData;
                    $hasNewData = true;
                }
                $this->userScoresLoading[$item['id']] = false;
            }
        }

        // Cache complete rankings after loading new data
        if ($hasNewData) {
            $this->cacheCompleteRankings($steamId);
        }
    }


    private function loadTop10DataSync($leaderboardName): void
    {
        $service = new SteamLeaderboardService();
        $leaderboardId = $this->getLeaderboardId($leaderboardName);

        if ($leaderboardId) {
            $entries = $service->getLeaderboardEntries($leaderboardId, 10);
            if (!empty($entries)) {
                $this->topEntries = $entries;
            }
        }

        $this->modalLoading = false;
    }

    private function loadAroundMeDataSync($leaderboardName): void
    {
        $service = new SteamLeaderboardService();
        $leaderboardId = $this->getLeaderboardId($leaderboardName);

        if ($leaderboardId && Auth::check() && Auth::user()->steam_id) {
            $entries = $service->getLeaderboardEntriesAroundUser($leaderboardId, Auth::user()->steam_id, 10);
            if (!empty($entries)) {
                $this->aroundMeEntries = $entries;
            }
        }

        $this->modalLoading = false;
    }

    private function cacheCompleteRankings($steamId): void
    {
        // Only cache if all rankings have rank_data
        $allComplete = true;
        foreach ($this->rankings as $ranking) {
            if (!isset($ranking['rank_data'])) {
                $allComplete = false;
                break;
            }
        }

        if ($allComplete) {
            $cacheKey = "steam_user_complete_rankings_{$steamId}";
            Cache::put($cacheKey, $this->rankings, 1800); // 30 minutes
        }
    }

    public function viewTop10($leaderboardName)
    {
        $this->selectedLeaderboard = $leaderboardName;
        $this->selectedLeaderboardName = $this->getLeaderboardDisplayName($leaderboardName);
        $this->modalType = 'top10';
        $this->topEntries = [];
        $this->showModal = true;

        $leaderboardId = $this->getLeaderboardId($leaderboardName);
        if ($leaderboardId) {
            // Try Cache::pull first (retrieve and remove)
            $cacheKey = "steam_leaderboard_entries_{$leaderboardId}_10";
            $cachedEntries = Cache::pull($cacheKey);

            if ($cachedEntries !== null) {
                // Use cached data immediately
                $this->topEntries = $cachedEntries;
                $this->modalLoading = false;

                // Re-cache the data for future use
                Cache::put($cacheKey, $cachedEntries, 1800);
            } else {
                // Need to load data
                $this->modalLoading = true;
                $this->loadTop10DataSync($leaderboardName);
            }
        }
    }

    public function viewAroundMe($leaderboardName)
    {
        $this->selectedLeaderboard = $leaderboardName;
        $this->selectedLeaderboardName = $this->getLeaderboardDisplayName($leaderboardName);
        $this->modalType = 'aroundme';
        $this->aroundMeEntries = [];
        $this->showModal = true;

        $leaderboardId = $this->getLeaderboardId($leaderboardName);
        if ($leaderboardId && Auth::check() && Auth::user()->steam_id) {
            // Try Cache::pull first (retrieve and remove)
            $cacheKey = "steam_leaderboard_around_{$leaderboardId}_" . Auth::user()->steam_id . "_10";
            $cachedEntries = Cache::pull($cacheKey);

            if ($cachedEntries !== null) {
                // Use cached data immediately
                $this->aroundMeEntries = $cachedEntries;
                $this->modalLoading = false;

                // Re-cache the data for future use
                Cache::put($cacheKey, $cachedEntries, 1800);
            } else {
                // Need to load data
                $this->modalLoading = true;
                $this->loadAroundMeDataSync($leaderboardName);
            }
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalType = null;
        $this->selectedLeaderboard = null;
        $this->selectedLeaderboardName = null;
        $this->topEntries = [];
        $this->aroundMeEntries = [];
        $this->modalLoading = false;
    }

    private function getLeaderboardId($leaderboardName)
    {
        foreach ($this->rankings as $ranking) {
            if ($ranking['name'] === $leaderboardName) {
                return $ranking['id'] ?? null;
            }
        }
        return null;
    }

    private function getLeaderboardDisplayName($leaderboardName)
    {
        foreach ($this->rankings as $ranking) {
            if ($ranking['name'] === $leaderboardName) {
                return $ranking['display_name'] ?? $leaderboardName;
            }
        }
        return $leaderboardName;
    }

    public function getLevelImage($levelName)
    {
        $imageName = strtolower($levelName) . '.png';
        $imagePath = '/img/levels/' . $imageName;

        // Check if file exists in public directory
        if (file_exists(public_path($imagePath))) {
            return $imagePath;
        }

        // Fallback to a default image or return null
        return '/img/levels/default.png';
    }

    public function getRankGroup($percentile)
    {
        if ($percentile >= 99) {
            return [
                'name' => 'Legend',
                'color' => 'text-yellow-400',
                'bg' => 'bg-yellow-900',
                'border' => 'border-yellow-400'
            ];
        } elseif ($percentile >= 90) {
            return [
                'name' => 'Grand Master',
                'color' => 'text-purple-400',
                'bg' => 'bg-purple-900',
                'border' => 'border-purple-400'
            ];
        } elseif ($percentile >= 75) {
            return [
                'name' => 'Master',
                'color' => 'text-blue-400',
                'bg' => 'bg-blue-900',
                'border' => 'border-blue-400'
            ];
        } elseif ($percentile >= 50) {
            return [
                'name' => 'Intermediate',
                'color' => 'text-green-400',
                'bg' => 'bg-green-900',
                'border' => 'border-green-400'
            ];
        } else {
            return [
                'name' => 'Novice',
                'color' => 'text-gray-400',
                'bg' => 'bg-gray-700',
                'border' => 'border-gray-400'
            ];
        }
    }


    public function showShareUrl()
    {
        if (Auth::check() && Auth::user()->steam_id) {
            $steamId = Auth::user()->steam_id;
            $this->shareUrl = url('/leaderboard/' . $steamId);
            $this->shareModalVisible = true;
        }
    }

    public function closeShareUrl()
    {
        $this->shareModalVisible = false;
        $this->shareUrl = '';
    }

    public function showImageModal($levelName, $displayName)
    {
        $this->selectedImageUrl = $this->getLevelImage($levelName);
        $this->selectedImageName = $displayName;
        $this->imageModalVisible = true;
    }

    public function closeImageModal()
    {
        $this->imageModalVisible = false;
        $this->selectedImageUrl = '';
        $this->selectedImageName = '';
    }

    public function getFilteredRankings()
    {
        $rankings = $this->rankings;

        if ($this->showOnlyWithScores) {
            $rankings = array_filter($rankings, function($ranking) {
                return isset($ranking['rank_data']['percentile']);
            });
        }

        // Sort by rank (lowest rank number first)
        usort($rankings, function($a, $b) {
            $aRank = $a['rank_data']['rank'] ?? PHP_INT_MAX;
            $bRank = $b['rank_data']['rank'] ?? PHP_INT_MAX;
            return $aRank <=> $bRank;
        });

        return $rankings;
    }

    public function render()
    {
        return view('livewire.steam-leaderboard');
    }
}

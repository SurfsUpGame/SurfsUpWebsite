<?php

namespace App\Livewire;

use App\Services\SteamLeaderboardService;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cache;

class UserLeaderboard extends Component
{
    public $steamId;
    public $userName;
    public $userAvatar;
    public $rankings = [];
    public $loading = true;
    public $rankingsLoading = [];
    public $totalMaps = 0;
    public $imageModalVisible = false;
    public $selectedImageUrl = '';
    public $selectedImageName = '';

    public function mount($steamId, $user)
    {
        $this->steamId = $steamId;
        $this->userName = $user->name ?? 'Player';
        $this->userAvatar = $user->avatar ?? null;
        $this->loadLeaderboardsOnly();
        $this->dispatch('loadUserRankingsAsync');
    }

    public function loadLeaderboardsOnly(): void
    {
        $service = new SteamLeaderboardService();

        // Check for cached complete rankings first
        $completeRankingsCacheKey = "steam_user_complete_rankings_{$this->steamId}";
        $cachedCompleteRankings = Cache::get($completeRankingsCacheKey);

        if ($cachedCompleteRankings) {
            // Use complete cached rankings
            $this->rankings = $cachedCompleteRankings;
            $this->rankingsLoading = array_fill_keys(array_column($this->rankings, 'id'), false);
            $this->loading = false;
            return;
        }

        // Get all leaderboards (cached for 12 hours) - this is fast
        $leaderboards = $service->getLeaderboards();
        $this->rankings = [];
        $this->totalMaps = count($leaderboards);

        if (empty($leaderboards)) {
            $this->loading = false;
            return;
        }

        // Just load the leaderboards without user rankings - mark all as loading
        foreach ($leaderboards as $leaderboard) {
            if (isset($leaderboard['id'])) {
                $this->rankingsLoading[$leaderboard['id']] = true;
                $this->rankings[] = $leaderboard;
            }
        }

        $this->loading = false;
    }

    #[On('loadUserRankingsAsync')]
    public function loadUserRankingsAsync(): void
    {
        // Only load rankings that aren't already cached
        $idsToLoad = [];
        $service = new SteamLeaderboardService();

        foreach ($this->rankings as $index => $ranking) {
            if (isset($ranking['id'])) {
                // Check if user rank is already cached
                $cacheKey = "steam_leaderboard_rank_{$this->steamId}_{$ranking['id']}";
                $cachedRank = Cache::get($cacheKey);

                if ($cachedRank !== null) {
                    $this->rankings[$index]['rank_data'] = $cachedRank;
                    $this->rankingsLoading[$ranking['id']] = false;
                } else {
                    $idsToLoad[] = ['id' => $ranking['id'], 'index' => $index];
                }
            }
        }

        // Load user rankings asynchronously
        if (!empty($idsToLoad)) {
            $this->batchLoadUserRankings($idsToLoad);
        } else {
            $this->cacheCompleteRankings();
        }
    }

    public function batchLoadUserRankings($idsToLoad): void
    {
        $service = new SteamLeaderboardService();

        // Process in small batches to avoid timeouts
        $chunks = array_chunk($idsToLoad, 5);
        $hasNewData = false;

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $rankData = $service->getUserRank($this->steamId, $item['id']);
                if ($rankData) {
                    $this->rankings[$item['index']]['rank_data'] = $rankData;
                    $hasNewData = true;
                }
                $this->rankingsLoading[$item['id']] = false;
            }
        }

        // Cache complete rankings after loading new data
        if ($hasNewData) {
            $this->cacheCompleteRankings();
        }
    }

    private function cacheCompleteRankings(): void
    {
        // Only cache if all rankings have rank_data or are confirmed to have no data
        $allComplete = true;
        foreach ($this->rankings as $ranking) {
            if (isset($this->rankingsLoading[$ranking['id']]) && $this->rankingsLoading[$ranking['id']]) {
                $allComplete = false;
                break;
            }
        }

        if ($allComplete) {
            $cacheKey = "steam_user_complete_rankings_{$this->steamId}";
            Cache::put($cacheKey, $this->rankings, 1800); // 30 minutes
        }
    }

    public function getLevelImage($levelName)
    {
        $imageName = strtolower($levelName) . '.png';
        $imagePath = '/img/levels/' . $imageName;

        // Check if file exists in public directory
        if (file_exists(public_path($imagePath))) {
            return $imagePath;
        }

        // Fallback to a default image
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
        } elseif ($percentile > 0) {
            return [
                'name' => 'Novice',
                'color' => 'text-gray-400',
                'bg' => 'bg-gray-700',
                'border' => 'border-gray-400'
            ];
        } else {
            return [
                'name' => '-',
            ];
        }
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
        $rankings = array_filter($this->rankings, function($ranking) {
            return isset($ranking['rank_data']);
        });

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
        return view('livewire.user-leaderboard');
    }
}

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
            $service = new SteamLeaderboardService();

            // Get all leaderboards first
            $leaderboards = $service->getLeaderboards();
            $this->rankings = [];

            // Load cached scores first, then mark remaining for async loading
            foreach ($leaderboards as $leaderboard) {
                if (isset($leaderboard['id'])) {
                    // Check cache first
                    $cacheKey = "steam_leaderboard_rank_" . Auth::user()->steam_id . "_" . $leaderboard['id'];
                    $cachedRank = Cache::get($cacheKey);
                    
                    if ($cachedRank) {
                        // Use cached data immediately
                        $leaderboard['rank_data'] = $cachedRank;
                        $this->userScoresLoading[$leaderboard['id']] = false;
                    } else {
                        // Mark for async loading
                        $this->userScoresLoading[$leaderboard['id']] = true;
                    }
                    
                    $this->rankings[] = $leaderboard;
                }
            }
        }

        $this->loading = false;

        // Now asynchronously load remaining user scores that weren't cached
        $this->loadUserScores();
    }

    public function loadUserScores(): void
    {
        if (!Auth::check() || !Auth::user()->steam_id) {
            return;
        }

        // Use JavaScript to trigger individual score loading
        $this->dispatch('startAsyncScoreLoading');
    }

    #[On('loadSingleUserScore')]
    public function loadSingleUserScore($leaderboardId, $index): void
    {
        if (!Auth::check() || !Auth::user()->steam_id) {
            return;
        }

        $service = new SteamLeaderboardService();
        $rankData = $service->getUserRank(Auth::user()->steam_id, $leaderboardId);

        if ($rankData) {
            $this->rankings[$index]['rank_data'] = $rankData;
        }

        $this->userScoresLoading[$leaderboardId] = false;
    }

    #[On('loadTop10DataAsync')]
    public function loadTop10DataAsync($leaderboardName): void
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

    #[On('loadAroundMeDataAsync')]
    public function loadAroundMeDataAsync($leaderboardName): void
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

    public function viewTop10($leaderboardName)
    {
        $this->selectedLeaderboard = $leaderboardName;
        $this->selectedLeaderboardName = $this->getLeaderboardDisplayName($leaderboardName);
        $this->modalType = 'top10';
        $this->modalLoading = true;
        $this->topEntries = [];
        
        // Open modal immediately
        $this->showModal = true;
        
        // Check cache first
        $leaderboardId = $this->getLeaderboardId($leaderboardName);
        if ($leaderboardId) {
            $cacheKey = "steam_leaderboard_entries_{$leaderboardId}_10";
            $cachedEntries = Cache::get($cacheKey);
            
            if ($cachedEntries) {
                // Use cached data immediately
                $this->topEntries = $cachedEntries;
                $this->modalLoading = false;
            } else {
                // Load data asynchronously
                $this->dispatch('loadTop10DataAsync', $leaderboardName);
            }
        }
    }

    public function viewAroundMe($leaderboardName)
    {
        $this->selectedLeaderboard = $leaderboardName;
        $this->selectedLeaderboardName = $this->getLeaderboardDisplayName($leaderboardName);
        $this->modalType = 'aroundme';
        $this->modalLoading = true;
        $this->aroundMeEntries = [];
        
        // Open modal immediately
        $this->showModal = true;
        
        // Check cache first
        $leaderboardId = $this->getLeaderboardId($leaderboardName);
        if ($leaderboardId && Auth::check() && Auth::user()->steam_id) {
            $cacheKey = "steam_leaderboard_around_{$leaderboardId}_" . Auth::user()->steam_id . "_10";
            $cachedEntries = Cache::get($cacheKey);
            
            if ($cachedEntries) {
                // Use cached data immediately
                $this->aroundMeEntries = $cachedEntries;
                $this->modalLoading = false;
            } else {
                // Load data asynchronously
                $this->dispatch('loadAroundMeDataAsync', $leaderboardName);
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
                return isset($ranking['rank_data']);
            });
        }

        // Sort by rank (lowest rank number first)
        usort($rankings, function($a, $b) {
            $aRank = isset($a['rank_data']) ? $a['rank_data']['rank'] : PHP_INT_MAX;
            $bRank = isset($b['rank_data']) ? $b['rank_data']['rank'] : PHP_INT_MAX;
            return $aRank <=> $bRank;
        });

        return $rankings;
    }

    public function render()
    {
        return view('livewire.steam-leaderboard');
    }
}

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
    public $worldRecordsLoading = [];
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
    public $compactView = false;
    public $groupByPlayer = true;
    public $isExpanded = true;

    protected $leaderboardService;

    public function mount() : void
    {
        $this->loadLeaderboardsOnly();
        $this->dispatch('loadWorldRecordsAsync');
    }

    public function loadLeaderboardsOnly(): void
    {
        $service = new SteamLeaderboardService();

        // Check for cached complete world records first
        $completeRecordsCacheKey = "steam_complete_world_records";
        $cachedCompleteRecords = Cache::get($completeRecordsCacheKey);

        if ($cachedCompleteRecords) {
            // Use complete cached records
            $this->rankings = $cachedCompleteRecords;
            $this->worldRecordsLoading = array_fill_keys(array_column($this->rankings, 'id'), false);
            $this->loading = false;
            return;
        }

        // Get all leaderboards (cached for 12 hours) - this is fast
        $leaderboards = $service->getLeaderboards();
        $this->rankings = [];

        if (empty($leaderboards)) {
            $this->loading = false;
            return;
        }

        // Just load the leaderboards without world records - mark all as loading
        foreach ($leaderboards as $leaderboard) {
            if (isset($leaderboard['id'])) {
                $this->worldRecordsLoading[$leaderboard['id']] = true;
                $this->rankings[] = $leaderboard;
            }
        }

        $this->loading = false;
    }

    #[On('loadWorldRecordsAsync')]
    public function loadWorldRecordsAsync(): void
    {
        // Only load world records that aren't already cached
        $idsToLoad = [];
        $service = new SteamLeaderboardService();

        foreach ($this->rankings as $index => $ranking) {
            if (isset($ranking['id'])) {
                // Check if world record is already cached
                $cacheKey = "steam_leaderboard_world_record_{$ranking['id']}";
                $cachedRecord = Cache::get($cacheKey);

                if ($cachedRecord !== null) {
                    $this->rankings[$index]['world_record'] = $cachedRecord;
                    $this->worldRecordsLoading[$ranking['id']] = false;
                } else {
                    $idsToLoad[] = ['id' => $ranking['id'], 'index' => $index];
                }
            }
        }

        // Load world records asynchronously
        if (!empty($idsToLoad)) {
            $this->batchLoadWorldRecords($idsToLoad);
        } else {
            $this->cacheCompleteWorldRecords();
        }
    }

    public function loadWorldRecords(): void
    {
        $this->loading = true;
        $this->worldRecordsLoading = [];

        $service = new SteamLeaderboardService();

        // Check for cached complete world records first
        $completeRecordsCacheKey = "steam_complete_world_records";
        $cachedCompleteRecords = Cache::get($completeRecordsCacheKey);

        if ($cachedCompleteRecords) {
            // Use complete cached records
            $this->rankings = $cachedCompleteRecords;
            $this->worldRecordsLoading = array_fill_keys(array_column($this->rankings, 'id'), false);
            $this->loading = false;
            return;
        }

        // Get all leaderboards (cached for 12 hours)
        $leaderboards = $service->getLeaderboards();
        $this->rankings = [];

        if (empty($leaderboards)) {
            $this->loading = false;
            return;
        }

        // Process each leaderboard
        $idsToLoad = [];
        foreach ($leaderboards as $index => $leaderboard) {
            if (isset($leaderboard['id'])) {
                // Check if world record is already cached
                $cacheKey = "steam_leaderboard_world_record_{$leaderboard['id']}";
                $cachedRecord = Cache::get($cacheKey);

                if ($cachedRecord !== null) {
                    $leaderboard['world_record'] = $cachedRecord;
                    $this->worldRecordsLoading[$leaderboard['id']] = false;
                } else {
                    // Mark for loading
                    $this->worldRecordsLoading[$leaderboard['id']] = true;
                    $idsToLoad[] = ['id' => $leaderboard['id'], 'index' => count($this->rankings)];
                }

                $this->rankings[] = $leaderboard;
            }
        }

        // Show the leaderboards immediately with loading states
        $this->loading = false;

        // If there are records to load, load them
        if (!empty($idsToLoad)) {
            $this->dispatch('showWorldRecordLoading');
            $this->batchLoadWorldRecords($idsToLoad);
        } else {
            // Cache complete records if all data is available
            $this->cacheCompleteWorldRecords();
        }
    }

    public function batchLoadWorldRecords($idsToLoad): void
    {
        $service = new SteamLeaderboardService();

        // Process in small batches to avoid timeouts
        $chunks = array_chunk($idsToLoad, 5);
        $hasNewData = false;

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $worldRecord = $service->getWorldRecord($item['id']);
                if ($worldRecord) {
                    $this->rankings[$item['index']]['world_record'] = $worldRecord;
                    $hasNewData = true;
                }
                $this->worldRecordsLoading[$item['id']] = false;
            }
        }

        // Cache complete records after loading new data
        if ($hasNewData) {
            $this->cacheCompleteWorldRecords();
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

    private function cacheCompleteWorldRecords(): void
    {
        // Only cache if all rankings have world_record
        $allComplete = true;
        foreach ($this->rankings as $ranking) {
            if (!isset($ranking['world_record'])) {
                $allComplete = false;
                break;
            }
        }

        if ($allComplete) {
            $cacheKey = "steam_complete_world_records";
            Cache::put($cacheKey, $this->rankings, 3600); // 1 hour to match world record cache
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
            $cacheKey = "steam_leaderboard_entries_{$leaderboardId}_10";
            $cachedEntries = Cache::get($cacheKey);

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
            $cacheKey = "steam_leaderboard_around_{$leaderboardId}_" . Auth::user()->steam_id . "_10";
            $cachedEntries = Cache::get($cacheKey);

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
                return isset($ranking['world_record']);
            });
        }

        // Sort by display name
        usort($rankings, function($a, $b) {
            return strcmp($a['display_name'] ?? '', $b['display_name'] ?? '');
        });

        return $rankings;
    }

    public function getGroupedRankings()
    {
        $rankings = $this->getFilteredRankings();
        $grouped = [];

        // Group by player steam_id
        foreach ($rankings as $ranking) {
            if (isset($ranking['world_record']) && isset($ranking['world_record']['steam_id'])) {
                $steamId = $ranking['world_record']['steam_id'];
                if (!isset($grouped[$steamId])) {
                    $grouped[$steamId] = [
                        'player' => $ranking['world_record'],
                        'records' => []
                    ];
                }
                $grouped[$steamId]['records'][] = $ranking;
            }
        }

        // Sort by number of records (descending)
        uasort($grouped, function($a, $b) {
            return count($b['records']) - count($a['records']);
        });

        return $grouped;
    }

    public function toggleGrouping()
    {
        $this->groupByPlayer = !$this->groupByPlayer;
    }
    
    public function toggleExpanded()
    {
        $this->isExpanded = !$this->isExpanded;
    }
    
    public function isLoadingAnyWorldRecords()
    {
        if (empty($this->worldRecordsLoading)) {
            return false;
        }
        
        return in_array(true, $this->worldRecordsLoading, true);
    }

    public function render()
    {
        $view = $this->compactView ? 'livewire.steam-leaderboard-compact' : 'livewire.steam-leaderboard';
        return view($view);
    }
}

<?php

namespace App\Livewire;

use App\Services\SteamLeaderboardService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SteamLeaderboard extends Component
{
    public $rankings = [];
    public $loading = true;
    public $selectedLeaderboard = null;
    public $selectedLeaderboardName = null;
    public $topEntries = [];
    public $aroundMeEntries = [];
    public $showModal = false;
    public $modalType = null;
    public $showOnlyWithScores = false;

    protected $leaderboardService;

    public function mount() : void
    {
        $this->loadUserRankings();
    }

    public function loadUserRankings(): void
    {
        $this->loading = true;

        if (Auth::check() && Auth::user()->steam_id) {
            $service = new SteamLeaderboardService();

            // Get all leaderboards first
            $leaderboards = $service->getLeaderboards();
            $this->rankings = [];

            // For each leaderboard, get the user's rank
            foreach ($leaderboards as $leaderboard) {
                if (isset($leaderboard['id'])) {
                    $rankData = $service->getUserRank(Auth::user()->steam_id, $leaderboard['id']);

                    if ($rankData) {
                        $leaderboard['rank_data'] = $rankData;
                    }

                    $this->rankings[] = $leaderboard;
                }
            }
        }

        $this->loading = false;
    }

    public function viewTop10($leaderboardName)
    {
        $this->selectedLeaderboard = $leaderboardName;
        $this->selectedLeaderboardName = $this->getLeaderboardDisplayName($leaderboardName);
        $this->modalType = 'top10';

        $service = new SteamLeaderboardService();
        $leaderboardId = $this->getLeaderboardId($leaderboardName);

        if ($leaderboardId) {
            $this->topEntries = $service->getLeaderboardEntries($leaderboardId, 10);
        } else {
            $this->topEntries = [];
        }

        $this->showModal = true;
    }

    public function viewAroundMe($leaderboardName)
    {
        $this->selectedLeaderboard = $leaderboardName;
        $this->selectedLeaderboardName = $this->getLeaderboardDisplayName($leaderboardName);
        $this->modalType = 'aroundme';

        $service = new SteamLeaderboardService();
        $leaderboardId = $this->getLeaderboardId($leaderboardName);

        if ($leaderboardId && Auth::check() && Auth::user()->steam_id) {
            $this->aroundMeEntries = $service->getLeaderboardEntriesAroundUser($leaderboardId, Auth::user()->steam_id, 10);
        } else {
            $this->aroundMeEntries = [];
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalType = null;
        $this->selectedLeaderboard = null;
        $this->selectedLeaderboardName = null;
        $this->topEntries = [];
        $this->aroundMeEntries = [];
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

    public function getFilteredRankings()
    {
        if ($this->showOnlyWithScores) {
            return array_filter($this->rankings, function($ranking) {
                return isset($ranking['rank_data']);
            });
        }
        
        return $this->rankings;
    }

    public function render()
    {
        return view('livewire.steam-leaderboard');
    }
}

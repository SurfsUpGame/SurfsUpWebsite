<div class="bg-gray-800 rounded-lg p-6">
    <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
        <i class="fa-solid fa-trophy text-yellow-500"></i>
        SurfsUp Leaderboard Rankings
    </h3>

    @if($loading)
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
        </div>
    @else
        @auth
            <!-- Filter Toggle -->
            <div class="mb-4 flex items-center gap-3">
                <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                    <input type="checkbox" wire:model.live="showOnlyWithScores" class="rounded bg-gray-700 border-gray-600 text-green-600 focus:ring-green-500 focus:ring-offset-gray-800">
                    <span class="text-sm">Show only maps with scores</span>
                </label>
                <span class="text-xs text-gray-400">
                    ({{ count($this->getFilteredRankings()) }} of {{ count($rankings) }} maps)
                </span>
            </div>

            @if(count($this->getFilteredRankings()) > 0)
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-left bg-gray-700 rounded-lg">
                        <thead>
                            <tr class="text-gray-300 bg-gray-600">
                                <th class="p-3 text-left">
                                    <button wire:click="sortBy('name')" class="flex items-center gap-1 hover:text-white transition">
                                        Leaderboard
                                        @if($sortBy === 'name')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                        @else
                                            <i class="fas fa-sort text-xs opacity-50"></i>
                                        @endif
                                    </button>
                                </th>
                                <th class="p-3 text-center">
                                    <button wire:click="sortBy('rank')" class="flex items-center gap-1 hover:text-white transition mx-auto">
                                        Your Rank
                                        @if($sortBy === 'rank')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                        @else
                                            <i class="fas fa-sort text-xs opacity-50"></i>
                                        @endif
                                    </button>
                                </th>
                                <th class="p-3 text-center">
                                    <button wire:click="sortBy('percentile')" class="flex items-center gap-1 hover:text-white transition mx-auto">
                                        Rank Group
                                        @if($sortBy === 'percentile')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                        @else
                                            <i class="fas fa-sort text-xs opacity-50"></i>
                                        @endif
                                    </button>
                                </th>
                                <th class="p-3 text-center">
                                    <button wire:click="sortBy('score')" class="flex items-center gap-1 hover:text-white transition mx-auto">
                                        Your Score
                                        @if($sortBy === 'score')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                        @else
                                            <i class="fas fa-sort text-xs opacity-50"></i>
                                        @endif
                                    </button>
                                </th>
                                <th class="p-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->getFilteredRankings() as $ranking)
                                <tr class="border-b border-gray-600 hover:bg-gray-600 transition relative overflow-hidden" 
                                    style="background-image: linear-gradient(rgba(55, 65, 81, 0.85), rgba(55, 65, 81, 0.95)), url('{{ $this->getLevelImage($ranking['name']) }}'); background-size: cover; background-position: center;">
                                    <td class="p-3 relative z-10">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $this->getLevelImage($ranking['name']) }}" 
                                                 alt="{{ $ranking['display_name'] }}" 
                                                 class="w-12 h-12 rounded-lg object-cover border border-gray-400 drop-shadow-lg"
                                                 onerror="this.src='/img/levels/default.png'">
                                            <h4 class="text-white font-semibold drop-shadow-lg">{{ $ranking['display_name'] }}</h4>
                                        </div>
                                    </td>
                                    @if(isset($ranking['rank_data']))
                                        @php
                                            $rankGroup = $this->getRankGroup($ranking['rank_data']['percentile']);
                                        @endphp
                                        <td class="p-3 text-center relative z-10">
                                            <span class="text-lg font-bold text-green-400 drop-shadow-lg">#{{ number_format($ranking['rank_data']['rank']) }}</span>
                                        </td>
                                        <td class="p-3 text-center relative z-10">
                                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $rankGroup['color'] }} {{ $rankGroup['bg'] }} border {{ $rankGroup['border'] }} drop-shadow-lg">
                                                {{ $rankGroup['name'] }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-center text-white relative z-10">
                                            <span class="drop-shadow-lg">{{ number_format($ranking['rank_data']['score'] / 1000, 3) }}</span>
                                        </td>
                                    @else
                                        <td class="p-3 text-center text-gray-400 relative z-10">-</td>
                                        <td class="p-3 text-center text-gray-400 relative z-10">-</td>
                                        <td class="p-3 text-center text-gray-400 relative z-10">-</td>
                                    @endif
                                    <td class="p-3 text-center relative z-10">
                                        <div class="flex gap-2 justify-center">
                                            <button wire:click="viewTop10('{{ $ranking['name'] }}')"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition drop-shadow-lg">
                                                Top 10
                                            </button>
                                            <button wire:click="viewAroundMe('{{ $ranking['name'] }}')"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition drop-shadow-lg">
                                                Around Me
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-400 text-center py-4">No leaderboard data available</p>
            @endif
        @else
            <div class="text-center py-8">
                <p class="text-gray-400 mb-4">Login with Steam to see your leaderboard rankings</p>
                <a href="{{ route('auth.steam') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 px-6 py-3 rounded-lg text-white transition">
                    <i class="fa-brands fa-steam"></i> Login with Steam
                </a>
            </div>
        @endauth
    @endif

    <!-- Leaderboard Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-lg p-6 max-w-4xl w-full mx-4 max-h-96 overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-xl font-semibold text-white">
                        {{ $selectedLeaderboardName }} -
                        @if($modalType === 'top10')
                            Top 10 Players
                        @else
                            Players Around You
                        @endif
                    </h4>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-white text-2xl">
                        ×
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-gray-400 border-b border-gray-700">
                                <th class="pb-2 pr-4">Rank</th>
                                <th class="pb-2 pr-4">Player</th>
                                <th class="pb-2 pr-4">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($modalType === 'top10' && count($topEntries) > 0)
                                @foreach($topEntries as $entry)
                                    <tr class="border-b border-gray-700 hover:bg-gray-700 transition">
                                        <td class="py-3 pr-4">
                                            @if($entry['rank'] <= 3)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                                    @if($entry['rank'] == 1) bg-yellow-500
                                                    @elseif($entry['rank'] == 2) bg-gray-400
                                                    @else bg-orange-600
                                                    @endif text-gray-900 font-bold">
                                                    {{ $entry['rank'] }}
                                                </span>
                                            @else
                                                <span class="text-white">#{{ $entry['rank'] }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-white">{{ $entry['persona_name'] }}</td>
                                        <td class="py-3 pr-4 text-green-400">{{ number_format($entry['score'] / 1000, 2) }}</td>
                                    </tr>
                                @endforeach
                            @elseif($modalType === 'aroundme' && count($aroundMeEntries) > 0)
                                @foreach($aroundMeEntries as $entry)
                                    <tr class="border-b border-gray-700 hover:bg-gray-700 transition
                                        {{ $entry['is_current_user'] ?? false ? 'bg-blue-900 bg-opacity-50' : '' }}">
                                        <td class="py-3 pr-4">
                                            <span class="text-white {{ $entry['is_current_user'] ?? false ? 'font-bold text-blue-400' : '' }}">
                                                #{{ $entry['rank'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4 text-white {{ $entry['is_current_user'] ?? false ? 'font-bold text-blue-400' : '' }}">
                                            {{ $entry['persona_name'] }}
                                            @if($entry['is_current_user'] ?? false)
                                                <span class="text-blue-400 text-sm">(You)</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-green-400">{{ number_format($entry['score'] / 1000, 2) }}</td>
                                        <td class="py-3 text-gray-400">{{ $entry['details']['time'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400">No data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

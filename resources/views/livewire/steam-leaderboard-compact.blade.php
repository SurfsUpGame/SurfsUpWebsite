<div class="bg-gray-800 rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-trophy text-yellow-500"></i>
            SurfsUp World Records
        </h3>
        <div class="flex items-center gap-4">
            <button wire:click="toggleGrouping" 
                    class="flex items-center gap-2 text-sm text-gray-400 hover:text-white transition">
                <i class="fas fa-{{ $groupByPlayer ? 'layer-group' : 'list' }}"></i>
                <span>{{ $groupByPlayer ? 'Grouped by Player' : 'Show by Map' }}</span>
            </button>
            @auth
                @if(Auth::check() && Auth::user()->steam_id)
                    <a href="/leaderboard/{{ Auth::user()->steam_id }}" class="flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-clock"></i>
                        View Your Times
                    </a>
                @endif
            @endauth
            @guest
                <a href="{{ route('auth.steam') }}" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fa-brands fa-steam"></i>
                    Login for Your Rankings
                </a>
            @endguest
        </div>
    </div>

    @if($loading)
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
        </div>
    @else
        @if(count($rankings) > 0)
            @if($groupByPlayer)
                <!-- Grouped by Player View -->
                <div class="space-y-6">
                    @foreach($this->getGroupedRankings() as $steamId => $data)
                        <div class="bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <a href="/leaderboard/{{ $steamId }}" class="flex items-center gap-3 group">
                                    @if(isset($data['player']['avatar_url']))
                                        <img src="{{ $data['player']['avatar_url'] }}"
                                             alt="{{ $data['player']['persona_name'] }}"
                                             class="w-10 h-10 rounded-full">
                                    @endif
                                    <div>
                                        <h4 class="text-white font-semibold text-lg group-hover:text-yellow-400 transition-colors">
                                            {{ $data['player']['persona_name'] }}
                                        </h4>
                                        <p class="text-sm text-gray-400">
                                            {{ count($data['records']) }} World {{ count($data['records']) === 1 ? 'Record' : 'Records' }}
                                        </p>
                                    </div>
                                </a>
                                <div class="flex items-center gap-2">
                                    @for($i = 0; $i < min(count($data['records']), 3); $i++)
                                        <i class="fas fa-trophy text-yellow-400"></i>
                                    @endfor
                                    @if(count($data['records']) > 3)
                                        <span class="text-yellow-400 font-bold">+{{ count($data['records']) - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($data['records'] as $record)
                                    <div class="bg-gray-800 rounded p-3 flex items-center gap-3">
                                        <img src="{{ $this->getLevelImage($record['name']) }}"
                                             alt="{{ $record['display_name'] }}"
                                             class="w-8 h-8 rounded object-cover cursor-pointer hover:scale-110 transition-transform"
                                             wire:click="showImageModal({{ json_encode($record['name']) }}, {{ json_encode($record['display_name']) }})"
                                             onerror="this.src='/img/levels/default.png'">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-white text-sm font-medium truncate">{{ $record['display_name'] }}</p>
                                            <p class="text-yellow-400 text-sm font-semibold">
                                                {{ number_format($record['world_record']['score'] / 1000, 3) }}s
                                            </p>
                                        </div>
                                        <button wire:click="viewTop10('{{ $record['name'] }}')"
                                                class="text-blue-400 hover:text-blue-300 text-xs">
                                            <i class="fas fa-list"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Regular Table View -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-gray-700">
                                <th class="pb-3 pr-4 text-gray-400 font-medium text-sm">Map</th>
                                <th class="pb-3 pr-4 text-gray-400 font-medium text-sm">World Record Holder</th>
                                <th class="pb-3 pr-4 text-gray-400 font-medium text-sm text-right">Time</th>
                                <th class="pb-3 text-gray-400 font-medium text-sm text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->getFilteredRankings() as $ranking)
                                <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3 pr-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $this->getLevelImage($ranking['name']) }}"
                                                 alt="{{ $ranking['display_name'] }}"
                                                 class="w-10 h-10 rounded object-cover cursor-pointer hover:scale-110 transition-transform"
                                                 wire:click="showImageModal({{ json_encode($ranking['name']) }}, {{ json_encode($ranking['display_name']) }})"
                                                 onerror="this.src='/img/levels/default.png'">
                                            <span class="text-white font-medium text-sm">{{ $ranking['display_name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 pr-4">
                                        @if(isset($ranking['world_record']))
                                            <a href="/leaderboard/{{ $ranking['world_record']['steam_id'] }}"
                                               class="flex items-center gap-2 group">
                                                @if(isset($ranking['world_record']['avatar_url']))
                                                    <img src="{{ $ranking['world_record']['avatar_url'] }}"
                                                         alt="{{ $ranking['world_record']['persona_name'] }}"
                                                         class="w-6 h-6 rounded-full">
                                                @endif
                                                <span class="text-gray-300 group-hover:text-white transition-colors">{{ $ranking['world_record']['persona_name'] }}</span>
                                                <i class="fas fa-crown text-yellow-400 text-xs"></i>
                                            </a>
                                        @elseif(isset($worldRecordsLoading[$ranking['id']]) && $worldRecordsLoading[$ranking['id']])
                                            <div class="flex items-center gap-2">
                                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-500"></div>
                                                <span class="text-gray-500 text-sm">Loading...</span>
                                            </div>
                                        @else
                                            <span class="text-gray-500 text-sm">No record set</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4 text-right">
                                        @if(isset($ranking['world_record']))
                                            <span class="text-yellow-400 font-semibold">{{ number_format($ranking['world_record']['score'] / 1000, 3) }}s</span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="flex gap-2 justify-center">
                                            <button wire:click="viewTop10('{{ $ranking['name'] }}')"
                                                    class="text-blue-400 hover:text-blue-300 text-sm transition">
                                                Top 10
                                            </button>
                                            @auth
                                            <span class="text-gray-600">|</span>
                                            <button wire:click="viewAroundMe('{{ $ranking['name'] }}')"
                                                    class="text-green-400 hover:text-green-300 text-sm transition">
                                                Around You
                                            </button>
                                            @endauth
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            @if($this->isLoadingAnyWorldRecords())
                <div class="mt-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-green-500"></div>
                        <span class="text-gray-400 text-sm">Loading world records...</span>
                    </div>
                </div>
            @elseif(count($this->getFilteredRankings()) < count($rankings))
                <div class="mt-4 text-center">
                    <button wire:click="$set('showOnlyWithScores', false)" 
                            class="text-blue-400 hover:text-blue-300 text-sm transition">
                        Show all {{ count($rankings) }} maps
                    </button>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <p class="text-gray-400 mb-2">No leaderboard data available</p>
                <p class="text-gray-500 text-sm">This could be due to Steam API configuration issues or no leaderboards being set up for this game.</p>
            </div>
        @endif
    @endif

    <!-- Modals remain the same -->
    @include('livewire.partials.steam-leaderboard-modals')
</div>
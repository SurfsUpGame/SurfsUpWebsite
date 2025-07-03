<div class="bg-gray-800 rounded-lg p-6">
    <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
        <i class="fa-solid fa-trophy text-yellow-500"></i>
        {{ $userName }}'s Leaderboard Rankings
    </h3>

    <!-- Player Info -->
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if($userAvatar)
                <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="w-12 h-12 rounded-full border-2 border-green-500">
            @endif
            <div>
{{--                <span class="text-gray-300">--}}
{{--                    <i class="fas fa-trophy text-yellow-500"></i>--}}
{{--                    {{ count($this->getFilteredRankings()) }} / {{ $totalMaps }} maps completed--}}
{{--                </span>--}}
                <p class="text-xs text-gray-400">
                    Steam ID: {{ $steamId }} |
                    <a href="https://steamcommunity.com/profiles/{{ $steamId }}" target="_blank" class="text-blue-400 hover:text-blue-300 transition">
                        <i class="fa-brands fa-steam"></i> View Steam Profile
                    </a>
                </p>
            </div>
        </div>
        <button onclick="shareLeaderboard()" class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-share"></i>
            Share Leaderboard
        </button>
    </div>

    @if($loading)
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
        </div>
    @else
        @if(count($this->getFilteredRankings()) > 0)
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-left bg-gray-700 rounded-lg">
                    <thead>
                        <tr class="text-gray-300 bg-gray-600">
                            <th class="p-3 text-left">Leaderboard</th>
                            <th class="p-3 text-center">Rank</th>
                            <th class="p-3 text-center">Rank Group</th>
                            <th class="p-3 text-center">Score</th>
                            <th class="p-3 text-center">Percentile</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->getFilteredRankings() as $ranking)
                            @php
                                $imageName = strtolower($ranking['name']) . '.png';
                                $imagePath = '/img/levels/' . $imageName;
                                if (!file_exists(public_path($imagePath))) {
                                    $imagePath = '/img/levels/default.png';
                                }
                            @endphp
                            <tr class="border-b border-gray-600 hover:bg-gray-600 transition relative overflow-hidden"
                                style="background-image: linear-gradient(rgba(55, 65, 81, 0.85), rgba(55, 65, 81, 0.95)), url('{{ $imagePath }}'); background-size: cover; background-position: center;">

                                <td class="p-3 relative z-10">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $imagePath }}"
                                                 alt="{{ $ranking['display_name'] }}"
                                                 class="w-16 h-16 rounded-lg object-cover border-2 border-gray-400 cursor-pointer transition-transform hover:scale-105 shadow-lg"
                                                 wire:click="showImageModal('{{ $ranking['name'] }}', '{{ $ranking['display_name'] }}')"
                                                 onerror="this.src='/img/levels/default.png'">
                                        </div>
                                        <div>
                                            <h4 class="text-white font-semibold text-lg drop-shadow-lg">{{ $ranking['display_name'] }}</h4>
                                            <p class="text-gray-300 text-sm drop-shadow-lg">{{ ucfirst(str_replace('_', ' ', $ranking['name'])) }}</p>
                                        </div>
                                    </div>
                                </td>
                                @php
                                    $rankData = $ranking['rank_data'];
                                    $percentile = $rankData['percentile'] ?? null;
                                    $rankGroup = $this->getRankGroup($percentile);
                                @endphp
                                <td class="p-3 text-center relative z-10">
                                    @if(isset($rankData['rank']))
                                        <span class="text-lg font-bold text-green-400 drop-shadow-lg">#{{ number_format($rankData['rank']) }}</span>
                                    @else
                                        <span class="text-lg font-bold drop-shadow-lg">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center relative z-10">
                                    @if($rankGroup['name'] != '-')
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $rankGroup['color'] }} {{ $rankGroup['bg'] }} border {{ $rankGroup['border'] }} drop-shadow-lg">
                                            {{ $rankGroup['name'] }}
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold drop-shadow-lg">
                                            {{ $rankGroup['name'] }}
                                        </span>
                                    @endif

                                </td>
                                <td class="p-3 text-center text-white relative z-10">
                                    @if(isset($rankData['score']))
                                        <span class="drop-shadow-lg">{{ number_format($rankData['score'] / 1000, 3) }}</span>
                                    @else
                                        <span class="drop-shadow-lg">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center text-white relative z-10">
                                    <span class="drop-shadow-lg">{{ number_format($percentile, 1) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                @if($loading || count(array_filter($rankingsLoading, fn($loading) => $loading)) > 0)
                    <div class="flex items-center justify-center gap-3">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                        <p class="text-gray-400">Loading user's scores...</p>
                    </div>
                @else
                    <p class="text-gray-400 mb-2">This player hasn't set any scores on the leaderboards yet.</p>
                @endif
            </div>
        @endif
    @endif
</div>

<!-- Full Screen Image Modal -->
@if($imageModalVisible)
    <div class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[99999]" wire:click="closeImageModal">
        <div class="max-w-full max-h-full p-4 flex flex-col items-center">
            <div class="relative">
                <img src="{{ $selectedImageUrl }}"
                     alt="{{ $selectedImageName }}"
                     class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl"
                     onerror="this.src='/img/levels/default.png'"
                     wire:click.stop>
                <button wire:click="closeImageModal"
                        class="absolute top-4 right-4 text-white bg-black bg-opacity-50 hover:bg-opacity-75 rounded-full w-10 h-10 flex items-center justify-center text-xl transition">
                    ×
                </button>
            </div>
            <h3 class="text-white text-2xl font-bold mt-6 text-center drop-shadow-lg">{{ $selectedImageName }}</h3>
        </div>
    </div>
@endif

<script>
// Share functionality
function shareLeaderboard() {
    const modal = document.getElementById('shareModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

// Listen for async ranking loading events
document.addEventListener('livewire:init', () => {
    // Handle async user ranking loading
    Livewire.on('loadUserRankingsAsync', () => {
        setTimeout(() => {
            Livewire.dispatch('loadUserRankingsAsync');
        }, 100);
    });
});
</script>

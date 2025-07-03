<div class="bg-gray-800 rounded-lg p-6">
    <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
        <i class="fa-solid fa-trophy text-yellow-500"></i>
        SurfsUp World Records
    </h3>

    @if($loading)
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
        </div>
    @else
        @auth
            <!-- Filter Toggle and Share Button -->
            <div class="mb-4 flex items-center justify-between">
{{--                <div class="flex items-center gap-3">--}}
{{--                    <label class="flex items-center gap-2 text-gray-300 cursor-pointer">--}}
{{--                        <input type="checkbox" wire:model.live="showOnlyWithScores" class="rounded bg-gray-700 border-gray-600 text-green-600 focus:ring-green-500 focus:ring-offset-gray-800">--}}
{{--                        <span class="text-sm">Show only maps with world records</span>--}}
{{--                    </label>--}}
{{--                    <span class="text-xs text-gray-400">--}}
{{--                        ({{ count($this->getFilteredRankings()) }} of {{ count($rankings) }} maps)--}}
{{--                    </span>--}}
{{--                </div>--}}
                @if(Auth::check() && Auth::user()->steam_id)
                    <a href="/leaderboard/{{ Auth::user()->steam_id }}" class="flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-clock"></i>
                        View Your Times
                    </a>
                @endif
            </div>

            @if(count($rankings) > 0)
                @if(count($this->getFilteredRankings()) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-6">
                        @foreach($this->getFilteredRankings() as $ranking)
                            <div class="bg-gray-700 rounded-md overflow-hidden hover:bg-gray-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl relative"
                                 style="background-image: linear-gradient(rgba(55, 65, 81, 0.9), rgba(55, 65, 81, 0.95)), url('{{ $this->getLevelImage($ranking['name']) }}'); background-size: cover; background-position: center;">

                                <div class="relative z-10 p-3">
                                    <div class="flex flex-col items-center gap-2 mb-3">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $this->getLevelImage($ranking['name']) }}"
                                                 alt="{{ $ranking['display_name'] }}"
                                                 class="w-12 h-12 rounded-md object-cover border border-gray-400 cursor-pointer transition-transform hover:scale-105 shadow-sm"
                                                 wire:click="showImageModal({{ json_encode($ranking['name']) }}, {{ json_encode($ranking['display_name']) }})"
                                                 onerror="this.src='/img/levels/default.png'">
                                        </div>
                                        <div class="text-center">
                                            <h2 class="text-white font-medium text-sm drop-shadow-lg line-clamp-2">{{ $ranking['display_name'] }}</h2>
                                        </div>
                                    </div>

                                    @if(isset($ranking['world_record']))
                                        <div class="mb-2 text-center">
                                            <div class="flex items-center justify-center gap-1 mb-1">
                                                <i class="fas fa-crown text-yellow-400 text-xs"></i>
                                                <a href="/leaderboard/{{ $ranking['world_record']['steam_id'] }}"
                                                   class="flex items-center gap-1 text-white font-medium text-xs drop-shadow-lg hover:text-yellow-300 transition-colors duration-200">
                                                    @if(isset($ranking['world_record']['avatar_url']))
                                                        <img src="{{ $ranking['world_record']['avatar_url'] }}" 
                                                             alt="{{ $ranking['world_record']['persona_name'] }}" 
                                                             class="w-4 h-4 rounded-full border border-yellow-400">
                                                    @endif
                                                    <span class="truncate">{{ $ranking['world_record']['persona_name'] }}</span>
                                                </a>
                                            </div>
                                            <div class="text-sm font-bold text-yellow-400 drop-shadow-lg">
                                                {{ number_format($ranking['world_record']['score'] / 1000, 3) }}s
                                            </div>
                                        </div>
                                    @elseif(isset($worldRecordsLoading[$ranking['id']]) && $worldRecordsLoading[$ranking['id']])
                                        <div class="mb-2 text-center">
                                            <div class="flex items-center justify-center gap-2 mb-2">
                                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-500"></div>
                                                <span class="text-gray-400">Loading...</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-2 text-center">
                                            <div class="text-gray-400 text-sm">No world record available</div>
                                        </div>
                                    @endif

                                    <div class="flex gap-1 justify-center">
                                        <button wire:click="viewTop10('{{ $ranking['name'] }}')"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition flex-1">
                                            Top 10
                                        </button>
                                        <button wire:click="viewAroundMe('{{ $ranking['name'] }}')"
                                                class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs transition flex-1">
                                            Around You
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="flex justify-center items-center gap-3">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                            <span class="text-gray-400">Fetching latest world records</span>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <p class="text-gray-400 mb-2">No leaderboard data available</p>
                    <p class="text-gray-500 text-sm">This could be due to Steam API configuration issues or no leaderboards being set up for this game.</p>
                </div>
            @endif
        @endauth

        @guest
            @if(count($rankings) > 0)
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                            <input type="checkbox" wire:model.live="showOnlyWithScores" class="rounded bg-gray-700 border-gray-600 text-green-600 focus:ring-green-500 focus:ring-offset-gray-800">
                            <span class="text-sm">Show only maps with world records</span>
                        </label>
                        <span class="text-xs text-gray-400">
                            ({{ count($this->getFilteredRankings()) }} of {{ count($rankings) }} maps)
                        </span>
                    </div>
                    <a href="{{ route('auth.steam') }}" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fa-brands fa-steam"></i>
                        Login for Your Rankings
                    </a>
                </div>

                @if(count($this->getFilteredRankings()) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-6">
                        @foreach($this->getFilteredRankings() as $ranking)
                            <div class="bg-gray-700 rounded-lg overflow-hidden hover:bg-gray-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl relative"
                                 style="background-image: linear-gradient(rgba(55, 65, 81, 0.9), rgba(55, 65, 81, 0.95)), url('{{ $this->getLevelImage($ranking['name']) }}'); background-size: cover; background-position: center;">

                                <div class="relative z-10 p-3">
                                    <div class="flex flex-col items-center gap-2 mb-3">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $this->getLevelImage($ranking['name']) }}"
                                                 alt="{{ $ranking['display_name'] }}"
                                                 class="w-12 h-12 rounded-md object-cover border border-gray-400 cursor-pointer transition-transform hover:scale-105 shadow-sm"
                                                 wire:click="showImageModal({{ json_encode($ranking['name']) }}, {{ json_encode($ranking['display_name']) }})"
                                                 onerror="this.src='/img/levels/default.png'">
                                        </div>
                                        <div class="text-center">
                                            <h4 class="text-white font-medium text-sm drop-shadow-lg line-clamp-2">{{ $ranking['display_name'] }}</h4>
                                        </div>
                                    </div>

                                    @if(isset($ranking['world_record']))
                                        <div class="mb-2 text-center">
                                            <div class="flex items-center justify-center gap-1 mb-1">
                                                <i class="fas fa-crown text-yellow-400 text-xs"></i>
                                                <a href="/leaderboard/{{ $ranking['world_record']['steam_id'] }}"
                                                   class="flex items-center gap-1 text-white font-medium text-xs drop-shadow-lg hover:text-yellow-300 transition-colors duration-200">
                                                    @if(isset($ranking['world_record']['avatar_url']))
                                                        <img src="{{ $ranking['world_record']['avatar_url'] }}" 
                                                             alt="{{ $ranking['world_record']['persona_name'] }}" 
                                                             class="w-4 h-4 rounded-full border border-yellow-400">
                                                    @endif
                                                    <span class="truncate">{{ $ranking['world_record']['persona_name'] }}</span>
                                                </a>
                                            </div>
                                            <div class="text-sm font-bold text-yellow-400 drop-shadow-lg mb-1">
                                                {{ number_format($ranking['world_record']['score'] / 1000, 3) }}s
                                            </div>
                                            <button wire:click="viewTop10('{{ $ranking['name'] }}')"
                                                    class="text-blue-400 hover:text-blue-300 text-xs underline transition">
                                                View {{ number_format($ranking['entry_count']) }} times
                                            </button>
                                        </div>
                                    @elseif(isset($worldRecordsLoading[$ranking['id']]) && $worldRecordsLoading[$ranking['id']])
                                        <div class="mb-2 text-center">
                                            <div class="flex items-center justify-center gap-2 mb-2">
                                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-500"></div>
                                                <span class="text-gray-400">Loading...</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-2 text-center">
                                            <div class="text-gray-400 text-sm">No world record available</div>
                                        </div>
                                    @endif

                                    <div class="flex justify-center">
                                        <button wire:click="viewTop10('{{ $ranking['name'] }}')"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition w-full">
                                            View Top 10
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="flex justify-center items-center gap-3">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                            <span class="text-gray-400">Fetching latest world records</span>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <p class="text-gray-400 mb-2">No leaderboard data available</p>
                    <p class="text-gray-500 text-sm">This could be due to Steam API configuration issues or no leaderboards being set up for this game.</p>
                </div>
            @endif
        @endguest
    @endif

    <!-- Leaderboard Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeModal">
            <div class="bg-gray-800 rounded-lg p-6 max-w-4xl w-full mx-4 max-h-96 overflow-y-auto" wire:click.stop>
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
                            @if($modalLoading)
                                <tr>
                                    <td colspan="4" class="py-8 text-center">
                                        <div class="flex justify-center items-center">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                                            <span class="ml-3 text-gray-400">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            @elseif($modalType === 'top10' && count($topEntries) > 0)
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
                                    <td colspan="4" class="py-8 text-center text-gray-400">
                                        @if($modalType === 'top10')
                                            No top 10 data available
                                        @elseif($modalType === 'aroundme')
                                            No data available around your rank
                                        @else
                                            No data available
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Share URL Modal -->
    @if($shareModalVisible)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-lg p-6 max-w-lg w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-xl font-semibold text-white flex items-center gap-2">
                        <i class="fas fa-share text-purple-400"></i>
                        Share Your Leaderboard
                    </h4>
                    <button wire:click="closeShareUrl" class="text-gray-400 hover:text-white text-2xl">
                        ×
                    </button>
                </div>

                <div class="space-y-4">
                    <p class="text-gray-300 text-sm">
                        Share this URL with others to show off your leaderboard rankings:
                    </p>

                    <div class="flex items-center gap-2">
                        <input type="text"
                               value="{{ $shareUrl }}"
                               readonly
                               class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                               id="shareUrlInput">
                        <button onclick="copyToClipboard()"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                            <i class="fas fa-copy"></i>
                            Copy
                        </button>
                    </div>

                    <div class="flex gap-2">
                        <a href="https://twitter.com/intent/tweet?text=Check%20out%20my%20SurfsUp%20leaderboard%20rankings!&url={{ urlencode($shareUrl) }}"
                           target="_blank"
                           class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm transition">
                            <i class="fab fa-twitter"></i>
                            Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                           target="_blank"
                           class="flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-3 py-2 rounded text-sm transition">
                            <i class="fab fa-facebook"></i>
                            Facebook
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
</div>

<script>
function copyToClipboard() {
    const input = document.getElementById('shareUrlInput');
    const button = document.querySelector('button[onclick="copyToClipboard()"]');
    const originalText = button.innerHTML;

    input.select();
    input.setSelectionRange(0, 99999);

    // Try modern clipboard API first (HTTPS/localhost only)
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value).then(function() {
            showCopySuccess(button, originalText);
        }).catch(function() {
            // Fallback to execCommand
            const success = fallbackCopyToClipboard(input);
            if (success) showCopySuccess(button, originalText);
        });
    } else {
        // Fallback for non-secure contexts
        const success = fallbackCopyToClipboard(input);
        if (success) showCopySuccess(button, originalText);
    }
}

function fallbackCopyToClipboard(input) {
    try {
        input.focus();
        input.select();
        return document.execCommand('copy');
    } catch (err) {
        console.error('Fallback copy failed:', err);
        return false;
    }
}

function showCopySuccess(button, originalText) {
    button.innerHTML = '<i class="fas fa-check"></i> Copied!';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-purple-600');
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-purple-600');
    }, 2000);
}

// Listen for modal data loading events
document.addEventListener('livewire:init', () => {
    Livewire.on('loadTop10DataAsync', (leaderboardName) => {
        setTimeout(() => {
            Livewire.dispatch('loadTop10DataAsync', { leaderboardName });
        }, 100);
    });

    Livewire.on('loadAroundMeDataAsync', (leaderboardName) => {
        setTimeout(() => {
            Livewire.dispatch('loadAroundMeDataAsync', { leaderboardName });
        }, 100);
    });

    // Handle async world record loading
    Livewire.on('loadWorldRecordsAsync', () => {
        setTimeout(() => {
            Livewire.dispatch('loadWorldRecordsAsync');
        }, 100);
    });

    // Handle world record loading event
    Livewire.on('showWorldRecordLoading', () => {
        // Force a small delay to ensure the UI updates with loading spinners
        setTimeout(() => {
            // Trigger component refresh to show updated loading states
            Livewire.find('{{ $this->getId() }}').$refresh();
        }, 50);
    });
});
</script>

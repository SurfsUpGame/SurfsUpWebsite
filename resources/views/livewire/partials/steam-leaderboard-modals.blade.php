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
                                <td colspan="3" class="py-8 text-center">
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
                                    <td class="py-3 pr-4 text-white">
                                        <div class="flex items-center gap-2">
                                            @if(isset($entry['avatar_url']) && $entry['avatar_url'])
                                                <img src="{{ $entry['avatar_url'] }}" 
                                                     alt="{{ $entry['persona_name'] }}"
                                                     class="w-6 h-6 rounded-full">
                                            @endif
                                            {{ $entry['persona_name'] }}
                                        </div>
                                    </td>
                                    <td class="py-3 pr-4 text-green-400">{{ number_format($entry['score'] / 1000, 3) }}s</td>
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
                                        <div class="flex items-center gap-2">
                                            @if(isset($entry['avatar_url']) && $entry['avatar_url'])
                                                <img src="{{ $entry['avatar_url'] }}" 
                                                     alt="{{ $entry['persona_name'] }}"
                                                     class="w-6 h-6 rounded-full">
                                            @endif
                                            <span>
                                                {{ $entry['persona_name'] }}
                                                @if($entry['is_current_user'] ?? false)
                                                    <span class="text-blue-400 text-sm">(You)</span>
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 pr-4 text-green-400">{{ number_format($entry['score'] / 1000, 3) }}s</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="py-8 text-center text-gray-400">
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
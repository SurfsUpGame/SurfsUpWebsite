@if(count($liveStreams) > 0)
<section class="py-16 bg-gradient-to-br from-gray-800 to-gray-900">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                <span class="text-purple-400">Live</span> on Twitch
            </h2>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Check out these amazing streamers playing SurfsUp right now!
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @foreach($liveStreams as $stream)
            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-2xl transform hover:scale-105 transition-all duration-300 border border-purple-500/20 hover:border-purple-500/50">
                <div class="relative">
                    <img src="{{ $stream['thumbnail_url'] }}"
                         alt="{{ $stream['user_name'] }} stream thumbnail"
                         class="w-full h-48 object-cover">
                    <div class="absolute top-3 left-3 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold flex items-center">
                        <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
                        LIVE
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold text-white mb-2 truncate">
                        {{ $stream['user_name'] }}
                    </h3>
                    <p class="text-gray-300 text-sm mb-4 line-clamp-2">
                        {{ $stream['title'] }}
                    </p>

                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-400">
                            Started {{ \Carbon\Carbon::parse($stream['posted_at'])->diffForHumans() }}
                        </div>
                    </div>

                    <a href="{{ $stream['url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="mt-4 w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center group">
                        <i class="fab fa-twitch mr-2"></i>
                        Watch on Twitch
                        <i class="fas fa-external-link-alt ml-2 text-xs group-hover:translate-x-1 transition-transform duration-200"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-400 text-sm mb-4">Want to see your stream here?</p>
            <p class="text-gray-300">Just start streaming SurfsUp and you'll automatically appear!</p>
        </div>
    </div>
</section>
@endif

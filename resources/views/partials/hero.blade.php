@guest
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="/img/surfsup-hero.png" alt="SurfsUp Hero" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/50 via-gray-900/70 to-gray-900"></div>
    </div>

    <div class="relative z-10 text-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <img src="{{ asset('/img/new_logo.png') }}"
                     alt="SurfsUp Logo"
                     class="mx-auto h-32 sm:h-40 md:h-48 lg:h-56 xl:h-64 w-auto drop-shadow-2xl transform hover:scale-105 transition-transform duration-300">
            </div>

            <p class="text-xl sm:text-2xl text-gray-200 mb-8 max-w-2xl mx-auto">
                Free-to-Play High Speed Multiplayer Precision Platformer
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="https://bearlikelion.github.io/SurfsUpSDK/"
                   target="_blank"
                   class="group relative inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-700 px-8 py-4 rounded-lg text-white font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                    </svg>
                    <span>Map Making SDK</span>
                    <div class="absolute inset-0 rounded-lg bg-blue-400 opacity-0 group-hover:opacity-20 transition-opacity duration-200"></div>
                </a>

                <a href="{{ route('roadmap') }}"
                   class="group relative inline-flex items-center gap-3 bg-orange-600 hover:bg-orange-700 px-8 py-4 rounded-lg text-white font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                    <span>View Roadmap</span>
                    <div class="absolute inset-0 rounded-lg bg-orange-400 opacity-0 group-hover:opacity-20 transition-opacity duration-200"></div>
                </a>
            </div>

            <div class="mt-12 animate-bounce">
                <svg class="w-6 h-6 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>
        </div>
    </div>
</section>
@endguest

<section class="relative bg-gradient-to-b from-gray-900 to-gray-800 pt-24 pb-16">
    @auth
    <div class="absolute inset-0 z-0">
        <img src="/img/surfsup-hero.png" alt="SurfsUp Hero" class="w-full h-full object-cover opacity-20">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/70 to-gray-800"></div>
    </div>
    @endauth
    <div class="@auth relative z-10 @endauth container mx-auto px-4 sm:px-6 lg:px-8">
        @livewire('steam-leaderboard', ['compactView' => true])
    </div>
</section>

{{--@include('partials.live-streams')--}}

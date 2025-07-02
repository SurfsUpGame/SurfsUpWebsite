<header x-data="{ open: false, dropdownOpen: false }" class="fixed top-0 left-0 w-full z-50 bg-gray-900 shadow-lg">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2 text-white hover:text-blue-400 transition-colors duration-200">
                    <span class="text-2xl">🌊</span>
                    <span class="text-xl font-bold">SurfsUp</span>
                </a>
            </div>

            <button @click="open = !open" class="md:hidden text-gray-300 hover:text-white focus:outline-none focus:text-white">
                <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                    <path x-show="!open" fill-rule="evenodd" d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z"/>
                    <path x-show="open" fill-rule="evenodd" d="M18.278 16.864a1 1 0 0 1-1.414 1.414l-4.829-4.828-4.828 4.828a1 1 0 0 1-1.414-1.414l4.828-4.829-4.828-4.828a1 1 0 0 1 1.414-1.414l4.829 4.828 4.828-4.828a1 1 0 1 1 1.414 1.414l-4.828 4.829 4.828 4.828z"/>
                </svg>
            </button>

            <nav class="hidden md:flex md:items-center md:space-x-6">
                <a href="https://store.steampowered.com/app/3454830/SurfsUp/" target="_blank" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center space-x-2">
                    <i class="fab fa-steam"></i>
                    <span>Steam</span>
                </a>
                <a href="https://discord.com/invite/95XmYfPnwV" target="_blank" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center space-x-2">
                    <i class="fab fa-discord"></i>
                    <span>Discord</span>
                </a>
                <a href="https://bsky.app/profile/bearlikelion.com" target="_blank" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center space-x-2">
                    <i class="fab fa-bluesky"></i>
                    <span>Bsky</span>
                </a>
                <a href="https://twitter.com/bearlikelion" target="_blank" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center space-x-2">
                    <i class="fab fa-x-twitter"></i>
                    <span>X</span>
                </a>
                <a href="https://tiktok.com/@surfsup.game" target="_blank" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center space-x-2">
                    <i class="fab fa-tiktok"></i>
                    <span>TikTok</span>
                </a>
                <a href="https://youtube.com/@bearlikelion" target="_blank" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center space-x-2">
                    <i class="fab fa-youtube"></i>
                    <span>YouTube</span>
                </a>

                @auth
                    <div class="relative ml-3">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-gray-300 hover:text-white focus:outline-none transition-colors duration-200">
                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full border-2 border-blue-500">
                            <span>{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="dropdownOpen"
                             @click.away="dropdownOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-56 bg-gray-800 rounded-md shadow-lg py-1 z-50">
                            <a href="https://bearlikelion.github.io/SurfsUpSDK/" target="_blank" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                                <i class="fas fa-code mr-2"></i> Map Making SDK
                            </a>
                            <a href="https://trello.com/b/6w7tMciD/surfsup-roadmap" target="_blank" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                                <i class="fas fa-map mr-2"></i> View Roadmap
                            </a>
                            <a href="/leaderboard/{{ auth()->user()->steam_id }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                                <i class="fas fa-trophy mr-2"></i> View My Leaderboard
                            </a>
                            <hr class="my-1 border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('auth.steam') }}" class="ml-4 bg-green-600 hover:bg-green-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200 flex items-center space-x-2">
                        <i class="fab fa-steam"></i>
                        <span>Login with Steam</span>
                    </a>
                @endauth
            </nav>
        </div>

        <nav x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="md:hidden bg-gray-800 border-t border-gray-700">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="https://store.steampowered.com/app/3454830/SurfsUp/" target="_blank" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <i class="fab fa-steam mr-2"></i> Steam
                </a>
                <a href="https://discord.com/invite/95XmYfPnwV" target="_blank" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <i class="fab fa-discord mr-2"></i> Discord
                </a>
                <a href="https://bsky.app/profile/bearlikelion.com" target="_blank" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <i class="fab fa-bluesky mr-2"></i> Bsky
                </a>
                <a href="https://twitter.com/bearlikelion" target="_blank" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <i class="fab fa-x-twitter mr-2"></i> X
                </a>
                <a href="https://tiktok.com/@surfsup.game" target="_blank" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <i class="fab fa-tiktok mr-2"></i> TikTok
                </a>
                <a href="https://youtube.com/@bearlikelion" target="_blank" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <i class="fab fa-youtube mr-2"></i> YouTube
                </a>
                
                @auth
                    <div class="border-t border-gray-700 mt-2 pt-2">
                        <div class="px-3 py-2 text-gray-400 text-sm flex items-center space-x-2">
                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-6 h-6 rounded-full border border-blue-500">
                            <span>{{ auth()->user()->name }}</span>
                        </div>
                        <a href="https://bearlikelion.github.io/SurfsUpSDK/" target="_blank" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                            <i class="fas fa-code mr-2"></i> Map Making SDK
                        </a>
                        <a href="https://trello.com/b/6w7tMciD/surfsup-roadmap" target="_blank" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                            <i class="fas fa-map mr-2"></i> View Roadmap
                        </a>
                        <a href="/leaderboard/{{ auth()->user()->steam_id }}" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                            <i class="fas fa-trophy mr-2"></i> View My Leaderboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded-md transition-colors duration-200">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                @else
                    <div class="px-3 py-2">
                        <a href="{{ route('auth.steam') }}" class="block w-full bg-green-600 hover:bg-green-700 px-4 py-2 rounded-md text-white font-medium text-center transition-colors duration-200">
                            <i class="fab fa-steam mr-2"></i> Login with Steam
                        </a>
                    </div>
                @endauth
            </div>
        </nav>
    </div>
</header>
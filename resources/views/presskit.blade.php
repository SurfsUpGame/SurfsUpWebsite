<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Press Kit - SurfsUp</title>
    <meta name="description" content="Download the SurfsUp press kit containing game assets, screenshots, logos, and information about the game.">

    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://kit.fontawesome.com/d251d3e9b0.js" crossorigin="anonymous"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H68DQ85G4C"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-H68DQ85G4C');
    </script>

    <script async defer data-website-id="abdc3690-c6b3-4646-a2d7-0ebb14a3dea6" src="https://unami.prod.arneman.me/mdnt.js"></script>
</head>
<body class="bg-gray-900 text-white antialiased">
    @include('partials.header')

    <main class="min-h-screen">
        <!-- Hero Section with Background -->
        <div class="relative bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('img/surfsup-hero.png') }}');">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-900/70 via-gray-900/80 to-gray-900"></div>
            <div class="relative container mx-auto px-4 py-32">
                <h1 class="text-6xl font-bold text-center mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-600">
                    Press Kit
                </h1>

                <p class="text-center text-gray-300 mb-12 max-w-2xl mx-auto text-lg">
                    Everything you need to cover SurfsUp - game assets, screenshots, logos, and detailed information about our high-speed multiplayer precision platformer.
                </p>
            </div>
        </div>

        <div class="container mx-auto px-4 py-16">

            <!-- Download Section -->
            <div class="text-center mb-16">
                <a href="{{ route('presskit.download') }}" class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-download"></i>
                    <span>Download Complete Press Kit (ZIP)</span>
                </a>
            </div>

            @if(session('error'))
                <div class="bg-red-600 text-white p-4 rounded-lg mb-8 text-center">
                    {{ session('error') }}
                </div>
            @endif

            <!-- About the Game -->
            <section class="mb-16">
                <h2 class="text-3xl font-bold mb-6 text-blue-400">About SurfsUp</h2>
                <div class="bg-gray-800 rounded-lg p-8">
                    <p class="text-gray-300 mb-4">
                        SurfsUp is a free-to-play high-speed multiplayer precision platformer that challenges players to master momentum-based movement and precise controls. Race against friends, set new records, and climb the global leaderboards in this adrenaline-fueled gaming experience.
                    </p>
                    <ul class="list-disc list-inside text-gray-300 space-y-2">
                        <li>Genre: Multiplayer Precision Platformer</li>
                        <li>Platform: PC (Windows, Mac, Linux)</li>
                        <li>Release Status: In Development</li>
                        <li>Price: Free-to-Play</li>
                        <li>Developer: [Your Studio Name]</li>
                        <li>Publisher: [Your Publisher Name]</li>
                    </ul>
                </div>
            </section>

            <!-- Key Features -->
            <section class="mb-16">
                <h2 class="text-3xl font-bold mb-6 text-purple-400">Key Features</h2>
                <div class="bg-gray-800 rounded-lg p-8">
                    <ul class="list-disc list-inside text-gray-300 space-y-2">
                        <li>High-speed momentum-based movement system</li>
                        <li>Competitive multiplayer races</li>
                        <li>Global leaderboards and ranking system</li>
                        <li>Regular content updates with new maps</li>
                        <li>Customizable character skins and cosmetics</li>
                        <li>Built-in level editor and community workshop</li>
                        <li>Cross-platform multiplayer support</li>
                    </ul>
                </div>
            </section>

            <!-- Press Kit Contents -->
            <section class="mb-16">
                <h2 class="text-3xl font-bold mb-6 text-green-400">Press Kit Contents</h2>
                <div class="bg-gray-800 rounded-lg p-8">
                    <p class="text-gray-300 mb-4">The downloadable press kit includes:</p>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-xl font-semibold mb-3 text-blue-300">Visual Assets</h3>
                            <ul class="list-disc list-inside text-gray-300 space-y-1 text-sm">
                                <li>Game logo (various formats and sizes)</li>
                                <li>High-resolution screenshots</li>
                                <li>Character artwork</li>
                                <li>Key art and promotional images</li>
                                <li>UI/UX showcase images</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-3 text-purple-300">Information & Media</h3>
                            <ul class="list-disc list-inside text-gray-300 space-y-1 text-sm">
                                <li>Detailed game description</li>
                                <li>Developer information</li>
                                <li>Fact sheet (PDF)</li>
                                <li>Press release template</li>
                                <li>Social media links</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Information -->
            <section class="mb-16">
                <h2 class="text-3xl font-bold mb-6 text-yellow-400">Press Contact</h2>
                <div class="bg-gray-800 rounded-lg p-8">
                    <p class="text-gray-300 mb-2">For press inquiries, interviews, or additional assets:</p>
                    <p class="text-xl text-white">
                        <i class="fas fa-envelope mr-2"></i>
                        press@surfsupgame.com
                    </p>
                </div>
            </section>

            <!-- Social Media -->
            <section>
                <h2 class="text-3xl font-bold mb-6 text-pink-400">Follow SurfsUp</h2>
                <div class="bg-gray-800 rounded-lg p-8">
                    <div class="flex flex-wrap gap-4 justify-center">
                        <a href="#" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition">
                            <i class="fab fa-steam"></i>
                            <span>Steam</span>
                        </a>
                        <a href="#" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition">
                            <i class="fab fa-twitter"></i>
                            <span>Twitter</span>
                        </a>
                        <a href="#" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition">
                            <i class="fab fa-discord"></i>
                            <span>Discord</span>
                        </a>
                        <a href="#" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition">
                            <i class="fab fa-youtube"></i>
                            <span>YouTube</span>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('partials.footer')

    @livewireScripts
</body>
</html>

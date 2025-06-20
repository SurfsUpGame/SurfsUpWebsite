<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SurfsUp</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">
{{--    <script src="https://cdn.tailwindcss.com"></script>--}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/d251d3e9b0.js" crossorigin="anonymous"></script>
    <style>
        .parallax {
            background-image: url('/img/surfsup-hero.png');
            min-height: 100vh;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
</head>
<body class="text-white bg-gray-900">

    <!-- Header -->
    <header x-data="{ open: false }" class="fixed top-0 left-0 w-full z-50 px-6 py-4 flex justify-between items-center bg-gray-800 bg-opacity-0 backdrop-blur-md text-white shadow-md">
        <h1 class="text-2xl font-bold">
            <a href="/" class="inline-flex items-center gap-2">
                🌊 <span>SurfsUp</span>
            </a>
        </h1>

        <!-- Hamburger Icon (Mobile Only) -->
        <button @click="open = !open" class="md:hidden text-white focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Social Links -->
        <div :class="{'block': open, 'hidden': !open}" class="absolute top-full left-0 w-full bg-gray-800 shadow-md md:shadow-none md:bg-transparent md:static md:flex md:flex-wrap md:justify-end gap-4 md:gap-6 px-6 py-4 md:py-0 hidden transition-all duration-300 ease-in-out">
            <a href="https://store.steampowered.com/app/3454830/SurfsUp/" target="_blank" class="flex items-center gap-2 hover:underline transition">
                <i class="fa-brands fa-steam"></i> Steam
            </a>
            <a href="https://bsky.app/profile/bearlikelion.com" target="_blank" class="flex items-center gap-2 hover:underline transition">
                <i class="fa-brands fa-bluesky"></i> Bsky
            </a>
            <a href="https://twitter.com/bearlikelion" target="_blank" class="flex items-center gap-2 hover:underline transition">
                <i class="fa-brands fa-x-twitter"></i> Twitter / X
            </a>
            <a href="https://tiktok.com/surfsup.game" target="_blank" class="flex items-center gap-2 hover:underline transition">
                <i class="fa-brands fa-tiktok"></i> Tiktok
            </a>
            <a href="https://youtube.com/@bearlikelion" target="_blank" class="flex items-center gap-2 hover:underline transition">
                <i class="fa-brands fa-youtube"></i> YouTube
            </a>
        </div>
    </header>


    <!-- Parallax Hero -->
    <section class="parallax flex pb-6 items-center justify-center">
        <div class="bg-gray-900 bg-opacity-60 p-8 rounded-xl text-center max-w-2xl">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">🌊 SurfsUp</h1>
            <p class="text-lg md:text-xl mb-6">
                Free-to-Play High Speed Multiplayer Precision Platformer
            </p>

            <!-- 🔗 Embedded Links in Hero -->
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="https://bearlikelion.github.io/SurfsUpSDK/" target="_blank" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg text-white transition">
                    <!-- Heroicon: Code Bracket -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 18l6-6-6-6M8 6l-6 6 6 6" />
                    </svg>
                    Map Making SDK
                </a>

                <a href="https://trello.com/b/6w7tMciD/surfsup-roadmap" target="_blank" class="flex items-center gap-2 bg-orange-600 hover:bg-orange-700 px-6 py-3 rounded-lg text-white transition">
                    <!-- Heroicon: Map -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5 2V6l5-2m0 16l6-2m-6 2V4m6 14l5 2V6l-5-2m0 16V4" />
                    </svg>
                    View Roadmap
                </a>
            </div>

        </div>
    </section>

    <!-- Embedded Media Panel -->
    <section class="bg-gray-900 py-16 px-6">
        <div class="max-w-6xl mx-auto text-center mb-10">
            <h2 class="text-3xl font-bold text-white mb-4">Watch & Wishlist</h2>
            <p class="text-gray-300">Catch the trailer, wishlist on Steam, watch development live on Twitch, and join the community on Discord!</p>
        </div>

        <!-- Twitch Embed -->
        <div class="w-full aspect-video max-w-3xl mx-auto rounded-lg mb-10 overflow-hidden">
            <p class="text-gray-300 text-center mb-6">Join the stream and chat while I develop SurfsUp live!</p>
            <iframe
                src="https://player.twitch.tv/?channel=bearlikelion&parent={{ request()->getHost() }}"
                frameborder="0"
                allowfullscreen="true"
                scrolling="no"
                height="100%"
                width="100%">
            </iframe>
        </div>

        <div class="flex flex-col lg:flex-row justify-center gap-8 max-w-6xl mx-auto">
            <!-- Left Column: YouTube + Steam -->
            <div class="flex-1 space-y-6">
                <!-- YouTube Embed -->
                <div class="w-full aspect-video rounded-lg overflow-hidden">
                    <iframe
                        class="w-full h-full"
                        src="https://www.youtube.com/embed/j2XA7omfhUc"
                        title="SurfsUp Trailer"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>

                <!-- Steam Widget -->
                <iframe
                    src="https://store.steampowered.com/widget/3454830/"
                    frameborder="0"
                    width="100%"
                    height="190"
                    class="rounded-lg w-full">
                </iframe>
            </div>

            <!-- Right Column: Discord -->
            <div class="flex-1">
                <iframe
                    src="https://discord.com/widget?id=1243644214105997373&theme=dark"
                    width="100%"
                    height="550"
                    allowtransparency="true"
                    frameborder="0"
                    sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
                    class="rounded-lg w-full">
                </iframe>
            </div>
        </div>
    </section>

    <!-- SurvivalScape Promo Section -->
    <section class="py-16 bg-gray-800">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Checkout Mark's First Game</h2>
            <p class="text-gray-300 mb-6">A competitive 2D roguelike auto shooter <strong>SurvivalScape</strong> is available on Steam.</p>

            <!-- Steam Widget Embed -->
            <div class="max-w-2xl mx-auto">
                <iframe
                    src="https://store.steampowered.com/widget/2862660/"
                    frameborder="0"
                    width="100%"
                    height="190"
                    class="rounded-lg shadow-lg border-gray-800">
                </iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-6 text-center text-sm text-gray-400">
        &copy; {{ date('Y') }} Mark Arneman. All rights reserved.
    </footer>


    <script src="//unpkg.com/alpinejs" defer></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H68DQ85G4C"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-H68DQ85G4C');
    </script>
</body>
</html>

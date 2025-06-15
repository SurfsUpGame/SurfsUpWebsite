<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SurfsUp</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/storage/img/favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .parallax {
            background-image: url('/storage/img/surfsup-hero.png');
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
    <header class="fixed top-0 left-0 w-full z-50 px-6 py-4 flex justify-between items-center bg-black bg-opacity-0 backdrop-blur-md text-white shadow-md">
        <h1 class="text-2xl font-bold">🌊 SurfsUp</h1>

        <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition">
            Login with Steam
        </a>
    </header>

    <!-- Parallax Hero -->
    <section class="parallax flex items-center justify-center">
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
                    SDK Project
                </a>

                <a href="/roadmap" class="flex items-center gap-2 bg-orange-600 hover:bg-orange-700 px-6 py-3 rounded-lg text-white transition">
                    <!-- Heroicon: Map -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5 2V6l5-2m0 16l6-2m-6 2V4m6 14l5 2V6l-5-2m0 16V4" />
                    </svg>
                    View Roadmap
                </a>
            </div>

        </div>
    </section>

    <!-- Social Links Section -->
    <section class="py-16 bg-gray-800">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-semibold mb-8">Follow & Play</h2>
            <div class="flex flex-wrap justify-center gap-6">

                <a href="https://store.steampowered.com/app/3454830/SurfsUp/" target="_blank" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg text-white transition">
                    <!-- Steam Icon (Heroicons doesn't have one; use a globe as placeholder) -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3C7.5 3 4 6.5 4 11c0 1.5.5 2.9 1.3 4l-1.3 4 4-1.3c1.1.8 2.5 1.3 4 1.3 4.5 0 8-3.5 8-8s-3.5-8-8-8z" />
                    </svg>
                    Steam
                </a>

                <a href="https://twitter.com/surfsupgame" target="_blank" class="flex items-center gap-2 bg-blue-400 hover:bg-blue-500 px-6 py-3 rounded-lg text-white transition">
                    <!-- Heroicon: Share -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 8a3 3 0 11-2.83-2H12A3 3 0 0115 8zM9 16a3 3 0 11-2.83-2H6A3 3 0 019 16zm12-4a3 3 0 11-2.83-2H18a3 3 0 013 3z" />
                    </svg>
                    Twitter / X
                </a>

                <a href="https://instagram.com/surfsupgame" target="_blank" class="flex items-center gap-2 bg-pink-500 hover:bg-pink-600 px-6 py-3 rounded-lg text-white transition">
                    <!-- Heroicon: Camera -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h3.28a1 1 0 01.948.684l.894 2.684a1 1 0 00.948.684H17a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                    </svg>
                    Instagram
                </a>

                <a href="https://youtube.com/@surfsupgame" target="_blank" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 px-6 py-3 rounded-lg text-white transition">
                    <!-- Heroicon: Play -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-5.197-3.027A1 1 0 008 9.027v5.946a1 1 0 001.555.858l5.197-3.027a1 1 0 000-1.736z" />
                    </svg>
                    YouTube
                </a>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-6 text-center text-sm text-gray-400">
        &copy; {{ date('Y') }} Mark Arneman. All rights reserved.
    </footer>
</body>
</html>

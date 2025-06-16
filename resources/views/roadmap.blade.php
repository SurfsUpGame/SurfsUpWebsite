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
<body class="text-white bg-blue-400 min-h-screen flex flex-col">

<!-- Header -->
<header class="fixed top-0 left-0 w-full z-50 px-6 py-4 flex justify-between items-center bg-black bg-opacity-40 backdrop-blur-md shadow-md">
    <h1 class="text-2xl font-bold"><a href="/">🌊 SurfsUp</a></h1>
{{--    <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition">--}}
{{--        Login with Steam--}}
{{--    </a>--}}
</header>

<!-- Main Content -->
<main class="flex-1 pt-28 px-6">
    <div class="max-w-7xl mx-auto p-6 rounded-xl overlay">
        <h2 class="text-3xl font-bold mb-6 text-center">🛣️ SurfsUp Roadmap</h2>

        <!-- Trello Embed -->
        <div class="w-full aspect-[4/3]">
            <div class="text-center mt-8">
                <a href="https://trello.com/b/6w7tMciD/surfsup-roadmap"
                   target="_blank"
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                    Roadmap on Trello
                </a>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="bg-gray-900 py-6 text-center text-sm text-gray-400">
    &copy; {{ date('Y') }} Mark Arneman. All rights reserved.
</footer>

@livewireScripts
@filamentScripts
</body>
</html>

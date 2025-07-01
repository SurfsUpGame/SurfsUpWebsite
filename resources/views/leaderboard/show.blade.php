<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name ?? 'Player' }}'s SurfsUp Leaderboard Rankings</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/d251d3e9b0.js" crossorigin="anonymous"></script>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $user->name ?? 'Player' }}'s SurfsUp Leaderboard Rankings">
    <meta property="og:description" content="Check out {{ $user->name ?? 'this player' }}'s impressive leaderboard rankings in SurfsUp!">
    <meta property="og:image" content="{{ asset('/img/surfsup-hero.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $user->name ?? 'Player' }}'s SurfsUp Leaderboard Rankings">
    <meta property="twitter:description" content="Check out {{ $user->name ?? 'this player' }}'s impressive leaderboard rankings in SurfsUp!">
    <meta property="twitter:image" content="{{ asset('/img/surfsup-hero.png') }}">
</head>
<body class="text-white bg-gray-900">

    <!-- Header -->
    <header class="px-6 py-4 flex justify-between items-center bg-gray-800 text-white shadow-md">
        <h1 class="text-2xl font-bold">
            <a href="/" class="inline-flex items-center gap-2">
                🌊 <span>SurfsUp</span>
            </a>
        </h1>

        <div class="flex items-center gap-4">
            <a href="https://store.steampowered.com/app/3454830/SurfsUp/" target="_blank" class="flex items-center gap-2 hover:underline transition">
                <i class="fa-brands fa-steam"></i> Steam
            </a>
            <a href="/" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition">
                View Your Rankings
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Player Info -->
            <div class="bg-gray-800 rounded-lg p-6 mb-6">
                <div class="flex items-center gap-4 mb-4">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full border-2 border-green-500">
                    @endif
                    <div>
                        <h2 class="text-3xl font-bold text-white">{{ $user->name ?? 'Player' }}'s Leaderboard Rankings</h2>
                        <p class="text-gray-400">Steam ID: {{ $steamId }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-gray-300">
                        <i class="fas fa-trophy text-yellow-500"></i>
                        {{ count($rankings) }} maps with scores
                    </span>
                    <button onclick="shareLeaderboard()" class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-share"></i>
                        Share
                    </button>
                </div>
            </div>

            <!-- Leaderboard Table -->
            @if(count($rankings) > 0)
                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left bg-gray-700 rounded-lg">
                            <thead>
                                <tr class="text-gray-300 bg-gray-600">
                                    <th class="p-3 text-left">Map</th>
                                    <th class="p-3 text-center">Rank</th>
                                    <th class="p-3 text-center">Rank Group</th>
                                    <th class="p-3 text-center">Score</th>
                                    <th class="p-3 text-center">Percentile</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rankings as $ranking)
                                    @if(isset($ranking['rank_data']['percentile']))
                                        @php
                                            $rankData = $ranking['rank_data'];
                                            $percentile = isset($rankData['percentile']) ? $rankData['percentile'] : null ;

                                            // Get rank group
                                            if ($percentile >= 99) {
                                                $rankGroup = ['name' => 'Legend', 'color' => 'text-yellow-400', 'bg' => 'bg-yellow-900', 'border' => 'border-yellow-400'];
                                            } elseif ($percentile >= 90) {
                                                $rankGroup = ['name' => 'Grand Master', 'color' => 'text-purple-400', 'bg' => 'bg-purple-900', 'border' => 'border-purple-400'];
                                            } elseif ($percentile >= 75) {
                                                $rankGroup = ['name' => 'Master', 'color' => 'text-blue-400', 'bg' => 'bg-blue-900', 'border' => 'border-blue-400'];
                                            } elseif ($percentile >= 50) {
                                                $rankGroup = ['name' => 'Intermediate', 'color' => 'text-green-400', 'bg' => 'bg-green-900', 'border' => 'border-green-400'];
                                            } elseif ($percentile > 0) {
                                                $rankGroup = ['name' => 'Novice', 'color' => 'text-gray-400', 'bg' => 'bg-gray-700', 'border' => 'border-gray-400'];
                                            } else {
                                                $rankGroup = ['name' => '-', 'color' => 'text-white-400', 'bg' => 'bg-none', 'border' => 'border-none'];
                                            }

                                            $imageName = strtolower($ranking['name']) . '.png';
                                            $imagePath = '/img/levels/' . $imageName;
                                            if (!file_exists(public_path($imagePath))) {
                                                $imagePath = '/img/levels/default.png';
                                            }
                                        @endphp
                                        <tr class="border-b border-gray-600 hover:bg-gray-600 transition relative overflow-hidden"
                                            style="background-image: linear-gradient(rgba(55, 65, 81, 0.85), rgba(55, 65, 81, 0.95)), url('{{ $imagePath }}'); background-size: cover; background-position: center;">
                                            <td class="p-3 relative z-10">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $imagePath }}"
                                                         alt="{{ $ranking['display_name'] }}"
                                                         class="w-12 h-12 rounded-lg object-cover border border-gray-400 drop-shadow-lg"
                                                         onerror="this.src='/img/levels/default.png'">
                                                    <h4 class="text-white font-semibold drop-shadow-lg">{{ $ranking['display_name'] }}</h4>
                                                </div>
                                            </td>
                                            <td class="p-3 text-center relative z-10">
                                                @if (isset($rankData['rank']))
                                                    <span class="text-lg font-bold text-green-400 drop-shadow-lg">#{{ number_format($rankData['rank']) }}</span>
                                                @else
                                                    <span class="text-lg drop-shadow-lg">-</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-center relative z-10">
                                                @if($rankGroup['name'] != '-')
                                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $rankGroup['color'] }} {{ $rankGroup['bg'] }} border {{ $rankGroup['border'] }} drop-shadow-lg">
                                                        {{ $rankGroup['name'] }}
                                                    </span>
                                                @else
                                                    <span class="inline-block px-3 py-1 rounded-full text-sm {{ $rankGroup['color'] }} {{ $rankGroup['bg'] }} border {{ $rankGroup['border'] }} drop-shadow-lg">
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
                                                @if($percentile > 0)
                                                    <span class="drop-shadow-lg">Top {{ number_format($percentile, 1) }}%</span>
                                                @else
                                                    <span class="drop-shadow-lg">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <p class="text-gray-400">This player hasn't set any scores on the leaderboards yet.</p>
                </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 py-6 text-center text-sm text-gray-400 mt-8">
        &copy; {{ date('Y') }} Mark Arneman. All rights reserved.
    </footer>

    <script>
        function shareLeaderboard() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $user->name ?? "Player" }}\'s SurfsUp Leaderboard Rankings',
                    text: 'Check out {{ $user->name ?? "this player" }}\'s impressive leaderboard rankings in SurfsUp!',
                    url: window.location.href
                });
            } else {
                // Fallback to clipboard
                navigator.clipboard.writeText(window.location.href).then(function() {
                    alert('URL copied to clipboard!');
                });
            }
        }
    </script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H68DQ85G4C"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-H68DQ85G4C');
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name ?? 'Player' }}'s SurfsUp Leaderboard Rankings</title>
    <meta name="description" content="Check out {{ $user->name ?? 'this player' }}'s impressive leaderboard rankings in SurfsUp!">
    <meta name="keywords" content="SurfsUp, leaderboard, rankings, {{ $user->name ?? 'player' }}, steam, indie game">

    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://kit.fontawesome.com/d251d3e9b0.js" crossorigin="anonymous"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

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

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H68DQ85G4C"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-H68DQ85G4C');
    </script>

    <script async defer data-website-id="abdc3690-c6b3-4646-a2d7-0ebb14a3dea6" src="https://unami.prod.arneman.me/umami.js"></script>
</head>
<body class="bg-gray-900 text-white antialiased">
    @include('partials.header')

    <main>
        <section class="bg-gradient-to-b from-gray-900 to-gray-800 pt-24 pb-16">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-6xl mx-auto">
                    @livewire('user-leaderboard', ['steamId' => $steamId, 'user' => $user])
                </div>
            </div>
        </section>
    </main>

    @include('partials.footer')

    <!-- Share URL Modal -->
    <div id="shareModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-gray-800 rounded-lg p-6 max-w-lg w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-xl font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-share text-purple-400"></i>
                    Share {{ $user->name ?? 'Player' }}'s Leaderboard
                </h4>
                <button onclick="closeShareModal()" class="text-gray-400 hover:text-white text-2xl">
                    ×
                </button>
            </div>

            <div class="space-y-4">
                <p class="text-gray-300 text-sm">
                    Share this URL with others to show off {{ $user->name ?? 'this player' }}'s leaderboard rankings:
                </p>

                <div class="flex items-center gap-2">
                    <input type="text"
                           value="{{ url()->current() }}"
                           readonly
                           class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                           id="shareUrlInput">
                    <button onclick="copyToClipboard()"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2"
                            id="copyButton">
                        <i class="fas fa-copy"></i>
                        Copy
                    </button>
                </div>

                <div class="flex gap-2">
                    <a href="https://twitter.com/intent/tweet?text=Check%20out%20{{ urlencode(($user->name ?? 'this player') . '\'s SurfsUp leaderboard rankings!') }}&url={{ urlencode(url()->current()) }}"
                       target="_blank"
                       class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm transition">
                        <i class="fab fa-twitter"></i>
                        Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                       target="_blank"
                       class="flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-3 py-2 rounded text-sm transition">
                        <i class="fab fa-facebook"></i>
                        Facebook
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Screen Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[99999] hidden" onclick="closeImageModal()">
        <div class="max-w-full max-h-full p-4 flex flex-col items-center">
            <div class="relative">
                <img id="modalImage"
                     src=""
                     alt=""
                     class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl"
                     onerror="this.src='/img/levels/default.png'"
                     onclick="event.stopPropagation()">
                <button onclick="closeImageModal()"
                        class="absolute top-4 right-4 text-white bg-black bg-opacity-50 hover:bg-opacity-75 rounded-full w-10 h-10 flex items-center justify-center text-xl transition">
                    ×
                </button>
            </div>
            <h3 id="modalImageName" class="text-white text-2xl font-bold mt-6 text-center drop-shadow-lg"></h3>
        </div>
    </div>

    <script>
        function shareLeaderboard() {
            const modal = document.getElementById('shareModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeShareModal() {
            const modal = document.getElementById('shareModal');
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
        }

        function copyToClipboard() {
            const input = document.getElementById('shareUrlInput');
            const button = document.getElementById('copyButton');
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

        function showImageModal(imagePath, displayName) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalImageName = document.getElementById('modalImageName');

            modalImage.src = imagePath;
            modalImage.alt = displayName;
            modalImageName.textContent = displayName;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
                closeShareModal();
            }
        });

        // Close share modal when clicking outside of it
        document.getElementById('shareModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeShareModal();
            }
        });
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

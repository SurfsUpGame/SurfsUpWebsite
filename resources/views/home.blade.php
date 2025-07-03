<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SurfsUp - Free-to-Play High Speed Multiplayer Precision Platformer</title>
    <meta name="description" content="SurfsUp is a free-to-play high speed multiplayer precision platformer. Wishlist now on Steam!">
    <meta name="keywords" content="SurfsUp, platformer, multiplayer, free to play, steam, indie game">

    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://kit.fontawesome.com/d251d3e9b0.js" crossorigin="anonymous"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H68DQ85G4C"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-H68DQ85G4C');
    </script>

    <script async defer data-website-id="abdc3690-c6b3-4646-a2d7-0ebb14a3dea6" src="https://unami.prod.arneman.me/dnt.js"></script>
</head>
<body class="bg-gray-900 text-white antialiased">
    @include('partials.header')

    <main>
        @include('partials.hero')

        @include('partials.live-streams')

        @include('partials.media-section')

        @include('partials.survivalscape-promo')
    </main>

    @include('partials.footer')
</body>
</html>

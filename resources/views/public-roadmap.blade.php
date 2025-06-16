<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SurfsUp Roadmap</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @filamentStyles
</head>
<body class="bg-gray-100 min-h-screen p-6">
<div class="max-w-8xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-3xl font-bold mb-6 text-center">
        <a href="/">🌊 SurfsUp</a>
    </h1>

    @livewire(App\Filament\Pages\RoadmapBoardPage::class)
</div>

@livewireScripts
@filamentScripts
</body>
</html>

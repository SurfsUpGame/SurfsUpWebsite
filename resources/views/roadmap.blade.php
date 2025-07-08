<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roadmap - SurfsUp</title>
    <meta name="description" content="Check out the development roadmap for SurfsUp">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://kit.fontawesome.com/d251d3e9b0.js" crossorigin="anonymous"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    @include('roadmap.partials.styles')
</head>
<body class="bg-gray-900 text-white antialiased min-h-screen flex flex-col" style="background-image: url('{{ asset('img/surfsup-hero.png') }}'); background-size: cover; background-position: center; background-attachment: fixed;" x-data="roadmapData()" x-init="console.log('Alpine.js loaded on body')">
    @include('partials.header')

    <main class="container mx-auto px-4 py-8 flex-grow bg-white/60 backdrop-blur-sm rounded-lg shadow-xl mt-20 mb-4">
        @if(session('success'))
            <div class="bg-green-600 text-white px-4 py-3 rounded-md mb-4 mt-16">
                {{ session('success') }}
            </div>
        @endif

        @include('roadmap.partials.header')

        <!-- Sprint-based Kanban Boards -->
        @foreach($sprints as $sprint)
            @if(!$showPast)
                @include('roadmap.partials.sprint-header-active', ['sprint' => $sprint])
            @endif
            
            <div class="mb-12" @if($showPast) x-data="{ expanded: false }" @endif>
                @if($showPast)
                    @include('roadmap.partials.sprint-header-past', ['sprint' => $sprint])
                @endif

                @include('roadmap.partials.sprint-kanban', [
                    'sprint' => $sprint,
                    'statuses' => $statuses,
                    'tasksByStatus' => $tasksByStatus,
                    'showPast' => $showPast
                ])
            </div>
        @endforeach

        @include('roadmap.partials.unassigned-tasks', [
            'statuses' => $statuses,
            'tasksByStatus' => $tasksByStatus
        ])

        @include('roadmap.partials.backlog-ideas', [
            'tasksByStatus' => $tasksByStatus,
            'showPast' => $showPast
        ])

        @include('roadmap.partials.user-suggestions', [
            'suggestions' => $suggestions
        ])

    </main>

    <!-- Modals outside main container for full screen coverage -->
    @include('roadmap.partials.create-task-modal', [
        'statuses' => $statuses,
        'eligibleUsers' => $eligibleUsers,
        'sprints' => $sprints,
        'epics' => $epics,
        'labels' => $labels,
        'errors' => $errors ?? collect()
    ])

    @include('roadmap.partials.task-details-modal', [
        'statuses' => $statuses,
        'eligibleUsers' => $eligibleUsers,
        'sprints' => $sprints,
        'epics' => $epics,
        'labels' => $labels
    ])

    @include('partials.footer')

    @include('roadmap.partials.scripts')
</body>
</html>
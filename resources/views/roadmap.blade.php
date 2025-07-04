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

    <style>
        [x-cloak] { display: none !important; }
        .kanban-column {
            flex: 1;
            min-width: 280px;
        }
        .kanban-card {
            cursor: move;
        }
        .kanban-card.dragging {
            opacity: 0.5;
        }
    </style>
</head>
<body class="bg-gray-900 text-white antialiased min-h-screen flex flex-col" style="background-image: url('{{ asset('img/surfsup-hero.png') }}'); background-size: cover; background-position: center; background-attachment: fixed;" x-data="roadmapData()" x-init="console.log('Alpine.js loaded on body')">
    @include('partials.header')

    <main class="container mx-auto px-4 py-8 flex-grow bg-white/60 backdrop-blur-sm rounded-lg shadow-xl mt-20 mb-4">
        @if(session('success'))
            <div class="bg-green-600 text-white px-4 py-3 rounded-md mb-4 mt-16">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between mt-16 mb-8">
            <h1 class="text-4xl font-bold text-center flex-1 text-gray-800">Development Roadmap</h1>
            @auth
                @if(auth()->user()->hasRole(['admin', 'staff']))
                    <button @click="showCreateModal = true" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Add Task</span>
                    </button>
                @endif
            @endauth
        </div>

        <!-- Sprint-based Kanban Boards -->
        @foreach($sprints as $sprint)
            <div class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">{{ $sprint->name }}</h2>
                        @if($sprint->description)
                            <p class="text-gray-600 mt-1">{{ $sprint->description }}</p>
                        @endif
                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                            @if($sprint->start_date)
                                <span><i class="fas fa-play mr-1"></i>{{ $sprint->start_date->format('M d, Y') }}</span>
                            @endif
                            @if($sprint->end_date)
                                <span><i class="fas fa-flag-checkered mr-1"></i>{{ $sprint->end_date->format('M d, Y') }}</span>
                            @endif
                            @if($sprint->is_active)
                                <span class="bg-green-600 px-2 py-1 rounded text-white text-xs">Active</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex overflow-x-auto gap-6 pb-4">
                    @foreach($statuses as $status)
                        @if(!in_array($status->value, ['backlog', 'ideas']))
                            @php
                                $sprintTasks = ($tasksByStatus[$status->value] ?? collect())->where('sprint_id', $sprint->id);
                            @endphp
                            <div class="kanban-column">
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <h3 class="text-xl font-semibold mb-4 text-{{ $status->getColor() }}-400">
                                        {{ $status->getTitle() }} ({{ $sprintTasks->count() }})
                                    </h3>

                                    <div class="space-y-3" id="column-{{ $status->value }}-sprint-{{ $sprint->id }}" data-status="{{ $status->value }}" data-sprint="{{ $sprint->id }}">
                                        @foreach($sprintTasks as $task)
                                            <div class="kanban-card bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors cursor-pointer"
                                                 @auth @if(auth()->user()->hasRole(['admin', 'staff'])) draggable="true" @endif @endauth
                                                 data-task-id="{{ $task->id }}"
                                                 @click="openTaskDetails({
                                                     id: {{ $task->id }},
                                                     title: '{{ addslashes($task->title) }}',
                                                     description: '{{ addslashes($task->description ?? '') }}',
                                                     status: '{{ $task->status->getTitle() }}',
                                                     status_value: '{{ $task->status->value }}',
                                                     assigned_user: '{{ $task->user ? addslashes($task->user->name) : 'Unassigned' }}',
                                                     assigned_user_id: {{ $task->user ? $task->user->id : 'null' }},
                                                     assigned_user_avatar: '{{ $task->user ? $task->user->avatar : '' }}',
                                                     assigned_user_initials: '{{ $task->user ? $task->user->initials() : 'U' }}',
                                                     creator: '{{ $task->creator ? addslashes($task->creator->name) : 'Unknown' }}',
                                                     creator_avatar: '{{ $task->creator ? $task->creator->avatar : '' }}',
                                                     creator_initials: '{{ $task->creator ? $task->creator->initials() : 'U' }}',
                                                     due_date: '{{ $task->due_date ? $task->due_date->format('M d, Y g:i A') : 'No due date' }}',
                                                     due_date_value: '{{ $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '' }}',
                                                     created_at: '{{ $task->created_at->format('M d, Y g:i A') }}',
                                                     sprint: '{{ $task->sprint ? addslashes($task->sprint->name) : 'No sprint' }}',
                                                     sprint_id: {{ $task->sprint_id ?? 'null' }},
                                                     epic: '{{ $task->epic ? addslashes($task->epic->name) : 'No epic' }}',
                                                     epic_id: {{ $task->epic_id ?? 'null' }},
                                                     priority: '{{ ucfirst($task->priority ?? 'medium') }}',
                                                     priority_value: '{{ $task->priority ?? 'medium' }}',
                                                     labels: {{ json_encode($task->labels->pluck('name')->toArray()) }},
                                                     label_ids: {{ json_encode($task->labels->pluck('id')->toArray()) }}
                                                 })">
                                                <!-- Priority indicator -->
                                                <div class="flex items-center justify-between mb-2">
                                                    <h4 class="font-semibold">{{ $task->title }}</h4>
                                                    @php
                                                        $priorityConfig = [
                                                            'low' => ['bg' => 'bg-green-500', 'text' => 'text-green-100'],
                                                            'medium' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-100'],
                                                            'high' => ['bg' => 'bg-orange-500', 'text' => 'text-orange-100'],
                                                            'critical' => ['bg' => 'bg-red-500', 'text' => 'text-red-100']
                                                        ];
                                                        $priority = $task->priority ?? 'medium';
                                                        $config = $priorityConfig[$priority];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                                        {{ ucfirst($priority) }}
                                                    </span>
                                                </div>

                                                @if($task->description)
                                                    <p class="text-sm text-gray-300 mb-2">{{ Str::limit($task->description, 80) }}</p>
                                                @endif

                                                <!-- Epic indicator -->
                                                @if($task->epic)
                                                    <div class="mb-2">
                                                        <span class="inline-block px-2 py-1 text-xs rounded" style="background-color: {{ $task->epic->color }}20; color: {{ $task->epic->color }}; border: 1px solid {{ $task->epic->color }}30;">
                                                            <i class="fas fa-flag mr-1"></i>{{ $task->epic->name }}
                                                        </span>
                                                    </div>
                                                @endif

                                                <!-- Labels -->
                                                @if($task->labels->count() > 0)
                                                    <div class="mb-2 flex flex-wrap gap-1">
                                                        @foreach($task->labels as $label)
                                                            <span class="inline-block px-2 py-1 text-xs rounded" style="background-color: {{ $label->color }}20; color: {{ $label->color }}; border: 1px solid {{ $label->color }}30;">
                                                                {{ $label->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <!-- Voting buttons -->
                                                @auth
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center space-x-2">
                                                            <button onclick="vote({{ $task->id }}, 1, event)" 
                                                                    class="vote-btn upvote flex items-center space-x-1 px-2 py-1 rounded text-xs transition-colors
                                                                           {{ $task->user_vote === 1 ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-green-600 hover:text-white' }}">
                                                                <i class="fas fa-thumbs-up"></i>
                                                                <span class="upvote-count">{{ $task->upvote_count }}</span>
                                                            </button>
                                                            <button onclick="vote({{ $task->id }}, -1, event)" 
                                                                    class="vote-btn downvote flex items-center space-x-1 px-2 py-1 rounded text-xs transition-colors
                                                                           {{ $task->user_vote === -1 ? 'bg-red-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-red-600 hover:text-white' }}">
                                                                <i class="fas fa-thumbs-down"></i>
                                                                <span class="downvote-count">{{ $task->downvote_count }}</span>
                                                            </button>
                                                        </div>
                                                        <div class="text-xs text-gray-400">
                                                            Score: <span class="vote-score font-semibold text-white">{{ $task->vote_score }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="flex items-center space-x-1 px-2 py-1 rounded text-xs bg-gray-600 text-gray-300">
                                                                <i class="fas fa-thumbs-up"></i>
                                                                <span>{{ $task->upvote_count }}</span>
                                                            </div>
                                                            <div class="flex items-center space-x-1 px-2 py-1 rounded text-xs bg-gray-600 text-gray-300">
                                                                <i class="fas fa-thumbs-down"></i>
                                                                <span>{{ $task->downvote_count }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="text-xs text-gray-400">
                                                            Score: <span class="font-semibold text-white">{{ $task->vote_score }}</span>
                                                        </div>
                                                    </div>
                                                @endauth

                                                <div class="flex items-center justify-between text-xs text-gray-400">
                                                    @if($task->user)
                                                        <div class="flex items-center space-x-2">
                                                            @if($task->user->avatar)
                                                                <img src="{{ $task->user->avatar }}" alt="{{ $task->user->name }}" class="w-5 h-5 rounded-full border border-gray-500">
                                                            @else
                                                                <div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-medium">
                                                                    {{ $task->user->initials() }}
                                                                </div>
                                                            @endif
                                                            <span>{{ $task->user->name }}</span>
                                                        </div>
                                                    @endif

                                                    @if($task->due_date)
                                                        <span>
                                                            <i class="fas fa-calendar mr-1"></i>
                                                            {{ $task->due_date->format('M d') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Tasks without Sprint -->
        @php
            $tasksWithoutSprint = collect($statuses)->mapWithKeys(function ($status) use ($tasksByStatus) {
                return [
                    $status->value => ($tasksByStatus[$status->value] ?? collect())->whereNull('sprint_id')
                ];
            })->filter(function($tasks) {
                return $tasks->count() > 0;
            });
        @endphp

        @if($tasksWithoutSprint->flatten()->count() > 0)
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Unassigned Tasks</h2>

                <div class="flex overflow-x-auto gap-6 pb-4">
                    @foreach($statuses as $status)
                        @if(!in_array($status->value, ['backlog', 'ideas']) && isset($tasksWithoutSprint[$status->value]) && $tasksWithoutSprint[$status->value]->count() > 0)
                            <div class="kanban-column">
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <h3 class="text-xl font-semibold mb-4 text-{{ $status->getColor() }}-400">
                                        {{ $status->getTitle() }} ({{ $tasksWithoutSprint[$status->value]->count() }})
                                    </h3>

                                    <div class="space-y-3" id="column-{{ $status->value }}-no-sprint" data-status="{{ $status->value }}" data-sprint="">
                                        @foreach($tasksWithoutSprint[$status->value] as $task)
                                            <div class="kanban-card bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors cursor-pointer"
                                                 @auth @if(auth()->user()->hasRole(['admin', 'staff'])) draggable="true" @endif @endauth
                                                 data-task-id="{{ $task->id }}"
                                                 @click="openTaskDetails({
                                                     id: {{ $task->id }},
                                                     title: '{{ addslashes($task->title) }}',
                                                     description: '{{ addslashes($task->description ?? '') }}',
                                                     status: '{{ $task->status->getTitle() }}',
                                                     status_value: '{{ $task->status->value }}',
                                                     assigned_user: '{{ $task->user ? addslashes($task->user->name) : 'Unassigned' }}',
                                                     assigned_user_id: {{ $task->user ? $task->user->id : 'null' }},
                                                     assigned_user_avatar: '{{ $task->user ? $task->user->avatar : '' }}',
                                                     assigned_user_initials: '{{ $task->user ? $task->user->initials() : 'U' }}',
                                                     creator: '{{ $task->creator ? addslashes($task->creator->name) : 'Unknown' }}',
                                                     creator_avatar: '{{ $task->creator ? $task->creator->avatar : '' }}',
                                                     creator_initials: '{{ $task->creator ? $task->creator->initials() : 'U' }}',
                                                     due_date: '{{ $task->due_date ? $task->due_date->format('M d, Y g:i A') : 'No due date' }}',
                                                     due_date_value: '{{ $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '' }}',
                                                     created_at: '{{ $task->created_at->format('M d, Y g:i A') }}',
                                                     sprint: 'No sprint',
                                                     sprint_id: null,
                                                     epic: '{{ $task->epic ? addslashes($task->epic->name) : 'No epic' }}',
                                                     epic_id: {{ $task->epic_id ?? 'null' }},
                                                     priority: '{{ ucfirst($task->priority ?? 'medium') }}',
                                                     priority_value: '{{ $task->priority ?? 'medium' }}',
                                                     labels: {{ json_encode($task->labels->pluck('name')->toArray()) }},
                                                     label_ids: {{ json_encode($task->labels->pluck('id')->toArray()) }}
                                                 })">
                                                <!-- Priority indicator -->
                                                <div class="flex items-center justify-between mb-2">
                                                    <h4 class="font-semibold">{{ $task->title }}</h4>
                                                    @php
                                                        $priorityConfig = [
                                                            'low' => ['bg' => 'bg-green-500', 'text' => 'text-green-100'],
                                                            'medium' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-100'],
                                                            'high' => ['bg' => 'bg-orange-500', 'text' => 'text-orange-100'],
                                                            'critical' => ['bg' => 'bg-red-500', 'text' => 'text-red-100']
                                                        ];
                                                        $priority = $task->priority ?? 'medium';
                                                        $config = $priorityConfig[$priority];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                                        {{ ucfirst($priority) }}
                                                    </span>
                                                </div>

                                                @if($task->description)
                                                    <p class="text-sm text-gray-300 mb-2">{{ Str::limit($task->description, 80) }}</p>
                                                @endif

                                                <!-- Epic indicator -->
                                                @if($task->epic)
                                                    <div class="mb-2">
                                                        <span class="inline-block px-2 py-1 text-xs rounded" style="background-color: {{ $task->epic->color }}20; color: {{ $task->epic->color }}; border: 1px solid {{ $task->epic->color }}30;">
                                                            <i class="fas fa-flag mr-1"></i>{{ $task->epic->name }}
                                                        </span>
                                                    </div>
                                                @endif

                                                <!-- Labels -->
                                                @if($task->labels->count() > 0)
                                                    <div class="mb-2 flex flex-wrap gap-1">
                                                        @foreach($task->labels as $label)
                                                            <span class="inline-block px-2 py-1 text-xs rounded" style="background-color: {{ $label->color }}20; color: {{ $label->color }}; border: 1px solid {{ $label->color }}30;">
                                                                {{ $label->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <!-- Voting buttons -->
                                                @auth
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center space-x-2">
                                                            <button onclick="vote({{ $task->id }}, 1, event)" 
                                                                    class="vote-btn upvote flex items-center space-x-1 px-2 py-1 rounded text-xs transition-colors
                                                                           {{ $task->user_vote === 1 ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-green-600 hover:text-white' }}">
                                                                <i class="fas fa-thumbs-up"></i>
                                                                <span class="upvote-count">{{ $task->upvote_count }}</span>
                                                            </button>
                                                            <button onclick="vote({{ $task->id }}, -1, event)" 
                                                                    class="vote-btn downvote flex items-center space-x-1 px-2 py-1 rounded text-xs transition-colors
                                                                           {{ $task->user_vote === -1 ? 'bg-red-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-red-600 hover:text-white' }}">
                                                                <i class="fas fa-thumbs-down"></i>
                                                                <span class="downvote-count">{{ $task->downvote_count }}</span>
                                                            </button>
                                                        </div>
                                                        <div class="text-xs text-gray-400">
                                                            Score: <span class="vote-score font-semibold text-white">{{ $task->vote_score }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="flex items-center space-x-1 px-2 py-1 rounded text-xs bg-gray-600 text-gray-300">
                                                                <i class="fas fa-thumbs-up"></i>
                                                                <span>{{ $task->upvote_count }}</span>
                                                            </div>
                                                            <div class="flex items-center space-x-1 px-2 py-1 rounded text-xs bg-gray-600 text-gray-300">
                                                                <i class="fas fa-thumbs-down"></i>
                                                                <span>{{ $task->downvote_count }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="text-xs text-gray-400">
                                                            Score: <span class="font-semibold text-white">{{ $task->vote_score }}</span>
                                                        </div>
                                                    </div>
                                                @endauth

                                                <div class="flex items-center justify-between text-xs text-gray-400">
                                                    @if($task->user)
                                                        <div class="flex items-center space-x-2">
                                                            @if($task->user->avatar)
                                                                <img src="{{ $task->user->avatar }}" alt="{{ $task->user->name }}" class="w-5 h-5 rounded-full border border-gray-500">
                                                            @else
                                                                <div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-medium">
                                                                    {{ $task->user->initials() }}
                                                                </div>
                                                            @endif
                                                            <span>{{ $task->user->name }}</span>
                                                        </div>
                                                    @endif

                                                    @if($task->due_date)
                                                        <span>
                                                            <i class="fas fa-calendar mr-1"></i>
                                                            {{ $task->due_date->format('M d') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Backlog and Ideas Sections Side by Side -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Backlog Section -->
            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-archive mr-2"></i>Backlog
                </h2>
                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="space-y-4" id="column-backlog" data-status="backlog">
                        @foreach($tasksByStatus['backlog'] ?? [] as $task)
                            <div class="kanban-card bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors cursor-pointer"
                                 @auth @if(auth()->user()->hasRole(['admin', 'staff'])) draggable="true" @endif @endauth
                                 data-task-id="{{ $task->id }}"
                                 @click="openTaskDetails({
                                     id: {{ $task->id }},
                                     title: '{{ addslashes($task->title) }}',
                                     description: '{{ addslashes($task->description ?? '') }}',
                                     status: '{{ $task->status->getTitle() }}',
                                     assigned_user: '{{ $task->user ? addslashes($task->user->name) : 'Unassigned' }}',
                                     creator: '{{ $task->creator ? addslashes($task->creator->name) : 'Unknown' }}',
                                     due_date: '{{ $task->due_date ? $task->due_date->format('M d, Y g:i A') : 'No due date' }}',
                                     created_at: '{{ $task->created_at->format('M d, Y g:i A') }}'
                                 })">
                                <h3 class="font-semibold mb-2">{{ $task->title }}</h3>

                                @if($task->description)
                                    <p class="text-sm text-gray-300 mb-2">{{ Str::limit($task->description, 120) }}</p>
                                @endif

                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    @if($task->user)
                                        <div class="flex items-center space-x-2">
                                            @if($task->user->avatar)
                                                <img src="{{ $task->user->avatar }}" alt="{{ $task->user->name }}" class="w-5 h-5 rounded-full border border-gray-500">
                                            @else
                                                <div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-medium">
                                                    {{ $task->user->initials() }}
                                                </div>
                                            @endif
                                            <span>{{ $task->user->name }}</span>
                                        </div>
                                    @endif

                                    @if($task->due_date)
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(empty($tasksByStatus['backlog']) || count($tasksByStatus['backlog']) === 0)
                        <div class="text-center text-gray-400 py-8">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p>No backlog items yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ideas Section -->
            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-lightbulb mr-2"></i>Ideas
                </h2>
                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="space-y-4" id="column-ideas" data-status="ideas">
                        @foreach($tasksByStatus['ideas'] ?? [] as $task)
                            <div class="kanban-card bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors cursor-pointer"
                                 @auth @if(auth()->user()->hasRole(['admin', 'staff'])) draggable="true" @endif @endauth
                                 data-task-id="{{ $task->id }}"
                                 @click="openTaskDetails({
                                     id: {{ $task->id }},
                                     title: '{{ addslashes($task->title) }}',
                                     description: '{{ addslashes($task->description ?? '') }}',
                                     status: '{{ $task->status->getTitle() }}',
                                     assigned_user: '{{ $task->user ? addslashes($task->user->name) : 'Unassigned' }}',
                                     creator: '{{ $task->creator ? addslashes($task->creator->name) : 'Unknown' }}',
                                     due_date: '{{ $task->due_date ? $task->due_date->format('M d, Y g:i A') : 'No due date' }}',
                                     created_at: '{{ $task->created_at->format('M d, Y g:i A') }}'
                                 })">
                                <h3 class="font-semibold mb-2">{{ $task->title }}</h3>

                                @if($task->description)
                                    <p class="text-sm text-gray-300 mb-2">{{ Str::limit($task->description, 120) }}</p>
                                @endif

                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    @if($task->user)
                                        <div class="flex items-center space-x-2">
                                            @if($task->user->avatar)
                                                <img src="{{ $task->user->avatar }}" alt="{{ $task->user->name }}" class="w-5 h-5 rounded-full border border-gray-500">
                                            @else
                                                <div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-medium">
                                                    {{ $task->user->initials() }}
                                                </div>
                                            @endif
                                            <span>{{ $task->user->name }}</span>
                                        </div>
                                    @endif

                                    @if($task->due_date)
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(empty($tasksByStatus['ideas']) || count($tasksByStatus['ideas']) === 0)
                        <div class="text-center text-gray-400 py-8">
                            <i class="fas fa-brain text-4xl mb-4"></i>
                            <p>No ideas yet - let your creativity flow!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </main>

    <!-- Modals outside main container for full screen coverage -->
    @auth
        @if(auth()->user()->hasRole(['admin', 'staff']))
            <!-- Create Task Modal -->
            <div x-show="showCreateModal"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click.away="showCreateModal = false"
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div @click.stop class="bg-gray-800 rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <h2 class="text-2xl font-bold mb-4">Create New Task</h2>

                    <form action="{{ route('roadmap.store') }}" method="POST">
                        @csrf

                        @if($errors->any())
                            <div class="mb-4 p-4 bg-red-600 rounded-md">
                                <ul class="text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Title and Description (Full Width) -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium mb-2">Title</label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   value="{{ old('title') }}"
                                   required
                                   class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium mb-2">Description</label>
                            <textarea name="description"
                                      id="description"
                                      rows="4"
                                      class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description') }}</textarea>
                        </div>

                        <!-- Two Column Layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium mb-2">Status</label>
                                    <select name="status"
                                            id="status"
                                            required
                                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->value }}" {{ old('status', 'todo') === $status->value ? 'selected' : '' }}>
                                                {{ $status->getTitle() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="user_id" class="block text-sm font-medium mb-2">Assign To</label>
                                    <select name="user_id"
                                            id="user_id"
                                            required
                                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select a user...</option>
                                        @foreach($eligibleUsers as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="sprint_id" class="block text-sm font-medium mb-2">Sprint (Optional)</label>
                                    <select name="sprint_id"
                                            id="sprint_id"
                                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">No sprint</option>
                                        @foreach($sprints as $sprint)
                                            <option value="{{ $sprint->id }}" {{ old('sprint_id') == $sprint->id ? 'selected' : '' }}>
                                                {{ $sprint->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="due_date" class="block text-sm font-medium mb-2">Due Date (Optional)</label>
                                    <input type="datetime-local"
                                           name="due_date"
                                           id="due_date"
                                           value="{{ old('due_date') }}"
                                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-4">
                                <div>
                                    <label for="epic_id" class="block text-sm font-medium mb-2">Epic (Optional)</label>
                                    <select name="epic_id"
                                            id="epic_id"
                                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">No epic</option>
                                        @foreach($epics as $epic)
                                            <option value="{{ $epic->id }}" {{ old('epic_id') == $epic->id ? 'selected' : '' }}>
                                                {{ $epic->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="priority" class="block text-sm font-medium mb-2">Priority</label>
                                    <select name="priority"
                                            id="priority"
                                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="low" {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', 'medium') === 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ old('priority', 'medium') === 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Labels (Optional)</label>
                                    <div class="max-h-32 overflow-y-auto bg-gray-700 border border-gray-600 rounded-md p-2">
                                        @foreach($labels as $label)
                                            <label class="flex items-center space-x-2 py-1">
                                                <input type="checkbox"
                                                       name="labels[]"
                                                       value="{{ $label->id }}"
                                                       {{ in_array($label->id, old('labels', [])) ? 'checked' : '' }}
                                                       class="rounded bg-gray-600 border-gray-500 text-blue-600 focus:ring-blue-500">
                                                <span class="inline-block px-2 py-1 text-xs rounded" style="background-color: {{ $label->color }}20; color: {{ $label->color }}; border: 1px solid {{ $label->color }}30;">
                                                    {{ $label->name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button"
                                    @click="showCreateModal = false"
                                    class="px-4 py-2 text-gray-300 hover:text-white transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                Create Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endauth

    <!-- Task Edit Modal -->
    <div x-show="showDetailsModal"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.away="showDetailsModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div @click.stop class="bg-gray-800 rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div x-show="selectedTask">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold" x-text="selectedTask ? selectedTask.title : ''"></h2>
                    <button @click="showDetailsModal = false" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                @auth
                    @if(auth()->user()->hasRole(['admin', 'staff']))
                        <form @submit.prevent="updateTask">
                            <!-- Title and Description (Full Width) -->
                            <div class="mb-6">
                                <label for="edit_title" class="block text-sm font-medium mb-2">Title</label>
                                <input type="text"
                                       id="edit_title"
                                       x-model="selectedTask.title"
                                       required
                                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div class="mb-6">
                                <label for="edit_description" class="block text-sm font-medium mb-2">Description</label>
                                <textarea id="edit_description"
                                          x-model="selectedTask.description"
                                          rows="4"
                                          class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>

                            <!-- Two Column Layout -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-4">
                                    <div>
                                        <label for="edit_status" class="block text-sm font-medium mb-2">Status</label>
                                        <select id="edit_status"
                                                x-model="selectedTask.status_value"
                                                required
                                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->value }}">{{ $status->getTitle() }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="edit_user_id" class="block text-sm font-medium mb-2">Assign To</label>
                                        <select id="edit_user_id"
                                                x-model="selectedTask.assigned_user_id"
                                                required
                                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Select a user...</option>
                                            @foreach($eligibleUsers as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="edit_sprint_id" class="block text-sm font-medium mb-2">Sprint (Optional)</label>
                                        <select id="edit_sprint_id"
                                                x-model="selectedTask.sprint_id"
                                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">No sprint</option>
                                            @foreach($sprints as $sprint)
                                                <option value="{{ $sprint->id }}">{{ $sprint->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="edit_due_date" class="block text-sm font-medium mb-2">Due Date (Optional)</label>
                                        <input type="datetime-local"
                                               id="edit_due_date"
                                               x-model="selectedTask.due_date_value"
                                               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-4">
                                    <div>
                                        <label for="edit_epic_id" class="block text-sm font-medium mb-2">Epic (Optional)</label>
                                        <select id="edit_epic_id"
                                                x-model="selectedTask.epic_id"
                                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">No epic</option>
                                            @foreach($epics as $epic)
                                                <option value="{{ $epic->id }}">{{ $epic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="edit_priority" class="block text-sm font-medium mb-2">Priority</label>
                                        <select id="edit_priority"
                                                x-model="selectedTask.priority_value"
                                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                            <option value="critical">Critical</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Labels (Optional)</label>
                                        <div class="max-h-32 overflow-y-auto bg-gray-700 border border-gray-600 rounded-md p-2">
                                            @foreach($labels as $label)
                                                <label class="flex items-center space-x-2 py-1">
                                                    <input type="checkbox"
                                                           :checked="selectedTask && selectedTask.label_ids && selectedTask.label_ids.includes({{ $label->id }})"
                                                           @change="toggleLabel({{ $label->id }})"
                                                           class="rounded bg-gray-600 border-gray-500 text-blue-600 focus:ring-blue-500">
                                                    <span class="inline-block px-2 py-1 text-xs rounded" style="background-color: {{ $label->color }}20; color: {{ $label->color }}; border: 1px solid {{ $label->color }}30;">
                                                        {{ $label->name }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-300 mb-2">Task Info</h3>
                                        <div class="text-xs text-gray-400 space-y-1">
                                            <p><i class="fas fa-user-plus mr-2"></i>Created by: <span x-text="selectedTask ? selectedTask.creator : ''"></span></p>
                                            <p><i class="fas fa-clock mr-2"></i>Created: <span x-text="selectedTask ? selectedTask.created_at : ''"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between mt-6">
                                <div class="space-x-3">
                                    <button type="button" @click="archiveTask(selectedTask.id)" class="bg-yellow-600 hover:bg-yellow-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                        <i class="fas fa-archive mr-2"></i>Archive
                                    </button>
                                    <button type="button" @click="deleteTask(selectedTask.id)" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                </div>
                                <div class="space-x-3">
                                    <button type="button"
                                            @click="showDetailsModal = false"
                                            class="px-4 py-2 text-gray-300 hover:text-white transition-colors duration-200">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                        <i class="fas fa-save mr-2"></i>Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <!-- Read-only view for non-admin users -->
                        <div class="space-y-4">
                            <div x-show="selectedTask && selectedTask.description">
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Description</h3>
                                <p class="text-gray-200" x-text="selectedTask ? selectedTask.description : ''"></p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-300 mb-1">Status</h3>
                                    <span class="inline-block px-3 py-1 bg-blue-600 text-white rounded-full text-sm" x-text="selectedTask ? selectedTask.status : ''"></span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-300 mb-1">Priority</h3>
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>
                                        <span class="text-gray-200" x-text="selectedTask ? selectedTask.priority : ''"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-300 mb-1">Sprint</h3>
                                    <p class="text-gray-200" x-text="selectedTask ? selectedTask.sprint : ''"></p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-300 mb-1">Epic</h3>
                                    <p class="text-gray-200" x-text="selectedTask ? selectedTask.epic : ''"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-300 mb-1">Assigned To</h3>
                                    <div class="flex items-center space-x-2" x-show="selectedTask && selectedTask.assigned_user && selectedTask.assigned_user !== 'Unassigned'">
                                        <div x-show="selectedTask && selectedTask.assigned_user_avatar">
                                            <img :src="selectedTask.assigned_user_avatar" :alt="selectedTask.assigned_user" class="w-6 h-6 rounded-full border border-gray-500">
                                        </div>
                                        <div x-show="selectedTask && !selectedTask.assigned_user_avatar && selectedTask.assigned_user_initials">
                                            <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-medium" x-text="selectedTask.assigned_user_initials"></div>
                                        </div>
                                        <span class="text-gray-200" x-text="selectedTask ? selectedTask.assigned_user : ''"></span>
                                    </div>
                                    <span x-show="!selectedTask || !selectedTask.assigned_user || selectedTask.assigned_user === 'Unassigned'" class="text-gray-400 italic">Unassigned</span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-300 mb-1">Due Date</h3>
                                    <div class="flex items-center text-gray-200" x-show="selectedTask && selectedTask.due_date && selectedTask.due_date !== 'No due date'">
                                        <i class="fas fa-calendar mr-2 text-blue-400"></i>
                                        <span x-text="selectedTask ? selectedTask.due_date : ''"></span>
                                    </div>
                                    <span x-show="!selectedTask || !selectedTask.due_date || selectedTask.due_date === 'No due date'" class="text-gray-400 italic">No due date</span>
                                </div>
                            </div>

                            <div x-show="selectedTask && selectedTask.labels && selectedTask.labels.length > 0">
                                <h3 class="text-sm font-semibold text-gray-300 mb-2">Labels</h3>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="label in (selectedTask ? selectedTask.labels : [])" :key="label">
                                        <span class="inline-block px-2 py-1 text-xs rounded bg-gray-600 text-gray-200 border border-gray-500" x-text="label"></span>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-2">Task Information</h3>
                                <div class="text-xs text-gray-400 space-y-1 bg-gray-700 p-3 rounded-md">
                                    <div class="flex items-center" x-show="selectedTask && selectedTask.creator">
                                        <i class="fas fa-user-plus mr-2 text-green-400"></i>
                                        <span>Created by: </span>
                                        <div class="flex items-center space-x-2 ml-1">
                                            <div x-show="selectedTask && selectedTask.creator_avatar">
                                                <img :src="selectedTask.creator_avatar" :alt="selectedTask.creator" class="w-4 h-4 rounded-full border border-gray-500">
                                            </div>
                                            <div x-show="selectedTask && !selectedTask.creator_avatar && selectedTask.creator_initials">
                                                <div class="w-4 h-4 rounded-full bg-green-600 flex items-center justify-center text-white text-xs font-medium" x-text="selectedTask.creator_initials"></div>
                                            </div>
                                            <span x-text="selectedTask ? selectedTask.creator : ''"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center" x-show="selectedTask && selectedTask.created_at">
                                        <i class="fas fa-clock mr-2 text-blue-400"></i>
                                        <span>Created: <span x-text="selectedTask ? selectedTask.created_at : ''"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button @click="showDetailsModal = false" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                                Close
                            </button>
                        </div>
                    @endif
                @else
                    <!-- Read-only view for guests -->
                    <div class="space-y-4">
                        <div x-show="selectedTask && selectedTask.description">
                            <h3 class="text-sm font-semibold text-gray-300 mb-1">Description</h3>
                            <p class="text-gray-200" x-text="selectedTask ? selectedTask.description : ''"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Status</h3>
                                <span class="inline-block px-3 py-1 bg-blue-600 text-white rounded-full text-sm" x-text="selectedTask ? selectedTask.status : ''"></span>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Priority</h3>
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>
                                    <span class="text-gray-200" x-text="selectedTask ? selectedTask.priority : ''"></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Sprint</h3>
                                <p class="text-gray-200" x-text="selectedTask ? selectedTask.sprint : ''"></p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Epic</h3>
                                <p class="text-gray-200" x-text="selectedTask ? selectedTask.epic : ''"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Assigned To</h3>
                                <div class="flex items-center space-x-2" x-show="selectedTask && selectedTask.assigned_user && selectedTask.assigned_user !== 'Unassigned'">
                                    <div x-show="selectedTask && selectedTask.assigned_user_avatar">
                                        <img :src="selectedTask.assigned_user_avatar" :alt="selectedTask.assigned_user" class="w-6 h-6 rounded-full border border-gray-500">
                                    </div>
                                    <div x-show="selectedTask && !selectedTask.assigned_user_avatar && selectedTask.assigned_user_initials">
                                        <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-medium" x-text="selectedTask.assigned_user_initials"></div>
                                    </div>
                                    <span class="text-gray-200" x-text="selectedTask ? selectedTask.assigned_user : ''"></span>
                                </div>
                                <span x-show="!selectedTask || !selectedTask.assigned_user || selectedTask.assigned_user === 'Unassigned'" class="text-gray-400 italic">Unassigned</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-1">Due Date</h3>
                                <div class="flex items-center text-gray-200" x-show="selectedTask && selectedTask.due_date && selectedTask.due_date !== 'No due date'">
                                    <i class="fas fa-calendar mr-2 text-blue-400"></i>
                                    <span x-text="selectedTask ? selectedTask.due_date : ''"></span>
                                </div>
                                <span x-show="!selectedTask || !selectedTask.due_date || selectedTask.due_date === 'No due date'" class="text-gray-400 italic">No due date</span>
                            </div>
                        </div>

                        <div x-show="selectedTask && selectedTask.labels && selectedTask.labels.length > 0">
                            <h3 class="text-sm font-semibold text-gray-300 mb-2">Labels</h3>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="label in (selectedTask ? selectedTask.labels : [])" :key="label">
                                    <span class="inline-block px-2 py-1 text-xs rounded bg-gray-600 text-gray-200 border border-gray-500" x-text="label"></span>
                                </template>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-300 mb-2">Task Information</h3>
                            <div class="text-xs text-gray-400 space-y-1 bg-gray-700 p-3 rounded-md">
                                <div class="flex items-center" x-show="selectedTask && selectedTask.creator">
                                    <i class="fas fa-user-plus mr-2 text-green-400"></i>
                                    <span>Created by: </span>
                                    <div class="flex items-center space-x-2 ml-1">
                                        <div x-show="selectedTask && selectedTask.creator_avatar">
                                            <img :src="selectedTask.creator_avatar" :alt="selectedTask.creator" class="w-4 h-4 rounded-full border border-gray-500">
                                        </div>
                                        <div x-show="selectedTask && !selectedTask.creator_avatar && selectedTask.creator_initials">
                                            <div class="w-4 h-4 rounded-full bg-green-600 flex items-center justify-center text-white text-xs font-medium" x-text="selectedTask.creator_initials"></div>
                                        </div>
                                        <span x-text="selectedTask ? selectedTask.creator : ''"></span>
                                    </div>
                                </div>
                                <div class="flex items-center" x-show="selectedTask && selectedTask.created_at">
                                    <i class="fas fa-clock mr-2 text-blue-400"></i>
                                    <span>Created: <span x-text="selectedTask ? selectedTask.created_at : ''"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button @click="showDetailsModal = false" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-md text-white font-medium transition-colors duration-200">
                            Close
                        </button>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    @include('partials.footer')

    <script>
        // Alpine.js component data
        function roadmapData() {
            return {
                showCreateModal: {{ isset($errors) && $errors->any() ? 'true' : 'false' }},
                showDetailsModal: false,
                selectedTask: null,
                openTaskDetails(task) {
                    this.selectedTask = task;
                    this.showDetailsModal = true;
                },
                archiveTask(taskId) {
                    if (confirm('Are you sure you want to archive this task?')) {
                        fetch(`/roadmap/task/${taskId}/archive`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.showDetailsModal = false;
                                location.reload();
                            } else {
                                alert('Failed to archive task');
                            }
                        })
                        .catch(error => {
                            console.error('Error archiving task:', error);
                            alert('Error archiving task');
                        });
                    }
                },
                deleteTask(taskId) {
                    if (confirm('Are you sure you want to permanently delete this task? This action cannot be undone.')) {
                        fetch(`/roadmap/task/${taskId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.showDetailsModal = false;
                                location.reload();
                            } else {
                                alert('Failed to delete task');
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting task:', error);
                            alert('Error deleting task');
                        });
                    }
                },
                toggleLabel(labelId) {
                    if (!this.selectedTask.label_ids) {
                        this.selectedTask.label_ids = [];
                    }
                    const index = this.selectedTask.label_ids.indexOf(labelId);
                    if (index > -1) {
                        this.selectedTask.label_ids.splice(index, 1);
                    } else {
                        this.selectedTask.label_ids.push(labelId);
                    }
                },
                updateTask() {
                    if (!this.selectedTask) return;

                    const formData = {
                        title: this.selectedTask.title,
                        description: this.selectedTask.description,
                        status: this.selectedTask.status_value,
                        user_id: this.selectedTask.assigned_user_id,
                        due_date: this.selectedTask.due_date_value,
                        sprint_id: this.selectedTask.sprint_id || null,
                        epic_id: this.selectedTask.epic_id || null,
                        priority: this.selectedTask.priority_value,
                        labels: this.selectedTask.label_ids || []
                    };

                    fetch(`/roadmap/task/${this.selectedTask.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.showDetailsModal = false;
                            location.reload();
                        } else {
                            alert('Failed to update task: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error updating task:', error);
                        alert('Error updating task');
                    });
                }
            };
        }

        // Simple drag and drop functionality
        let draggedElement = null;

        document.querySelectorAll('.kanban-card').forEach(card => {
            card.addEventListener('dragstart', (e) => {
                draggedElement = e.target;
                e.target.classList.add('dragging');
            });

            card.addEventListener('dragend', (e) => {
                e.target.classList.remove('dragging');
            });
        });

        // Add drop zones for columns (including empty ones)
        document.querySelectorAll('.kanban-column .bg-gray-800, .bg-gray-800:has(#column-backlog), .bg-gray-800:has(#column-ideas)').forEach(columnContainer => {
            const column = columnContainer.querySelector('[id^="column-"]');

            columnContainer.addEventListener('dragover', (e) => {
                e.preventDefault();
                columnContainer.classList.add('bg-gray-700'); // Visual feedback
            });

            columnContainer.addEventListener('dragleave', (e) => {
                // Only remove highlight if we're actually leaving the container
                if (!columnContainer.contains(e.relatedTarget)) {
                    columnContainer.classList.remove('bg-gray-700');
                }
            });

            columnContainer.addEventListener('drop', (e) => {
                e.preventDefault();
                columnContainer.classList.remove('bg-gray-700'); // Remove visual feedback

                if (!draggedElement) return;

                const taskId = draggedElement.dataset.taskId;
                const newStatus = column.dataset.status;
                const newSprintId = column.dataset.sprint;
                const currentStatus = draggedElement.closest('[id^="column-"]').dataset.status;
                const currentSprintId = draggedElement.closest('[id^="column-"]').dataset.sprint;

                // Don't do anything if dropped in the same column
                if (newStatus === currentStatus && newSprintId === currentSprintId) return;

                // Move the element visually first
                const afterElement = getDragAfterElement(column, e.clientY);
                if (afterElement == null) {
                    column.appendChild(draggedElement);
                } else {
                    column.insertBefore(draggedElement, afterElement);
                }

                // Prepare request body
                const requestBody = {
                    status: newStatus
                };

                // Add sprint_id to request body (null for unassigned, sprint ID for assigned)
                if (newSprintId === '') {
                    requestBody.sprint_id = null;
                } else {
                    requestBody.sprint_id = parseInt(newSprintId);
                }

                // Make AJAX call to update task status and sprint
                fetch(`/roadmap/task/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(requestBody)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Task updated successfully');
                        location.reload();
                    } else {
                        console.error('Failed to update task');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error updating task:', error);
                    location.reload();
                });
            });
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.kanban-card:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        // Voting function
        function vote(taskId, voteValue, event) {
            event.stopPropagation(); // Prevent opening task details modal
            
            fetch(`/roadmap/task/${taskId}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    vote: voteValue
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find the task card and update vote counts
                    const taskCard = event.target.closest('.kanban-card');
                    
                    // Update upvote count
                    const upvoteCount = taskCard.querySelector('.upvote-count');
                    if (upvoteCount) {
                        upvoteCount.textContent = data.upvote_count;
                    }
                    
                    // Update downvote count
                    const downvoteCount = taskCard.querySelector('.downvote-count');
                    if (downvoteCount) {
                        downvoteCount.textContent = data.downvote_count;
                    }
                    
                    // Update vote score
                    const voteScore = taskCard.querySelector('.vote-score');
                    if (voteScore) {
                        voteScore.textContent = data.vote_score;
                    }
                    
                    // Update button states
                    const upvoteBtn = taskCard.querySelector('.upvote');
                    const downvoteBtn = taskCard.querySelector('.downvote');
                    
                    // Reset button classes
                    upvoteBtn.className = upvoteBtn.className.replace(/(bg-green-600|text-white)/g, '').replace(/\s+/g, ' ').trim();
                    downvoteBtn.className = downvoteBtn.className.replace(/(bg-red-600|text-white)/g, '').replace(/\s+/g, ' ').trim();
                    
                    if (!upvoteBtn.className.includes('bg-gray-600')) {
                        upvoteBtn.className += ' bg-gray-600 text-gray-300 hover:bg-green-600 hover:text-white';
                    }
                    if (!downvoteBtn.className.includes('bg-gray-600')) {
                        downvoteBtn.className += ' bg-gray-600 text-gray-300 hover:bg-red-600 hover:text-white';
                    }
                    
                    // Apply active state based on user's current vote
                    if (data.user_vote === 1) {
                        upvoteBtn.className = upvoteBtn.className.replace(/(bg-gray-600|text-gray-300|hover:bg-green-600|hover:text-white)/g, '').replace(/\s+/g, ' ').trim();
                        upvoteBtn.className += ' bg-green-600 text-white';
                    } else if (data.user_vote === -1) {
                        downvoteBtn.className = downvoteBtn.className.replace(/(bg-gray-600|text-gray-300|hover:bg-red-600|hover:text-white)/g, '').replace(/\s+/g, ' ').trim();
                        downvoteBtn.className += ' bg-red-600 text-white';
                    }
                    
                    console.log('Vote registered successfully');
                } else {
                    alert('Failed to register vote: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error voting:', error);
                alert('Error voting on task');
            });
        }
    </script>
</body>
</html>

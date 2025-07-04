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
         sprint: '{{ $task->sprint ? addslashes($task->sprint->name) : ($sprint ?? 'No sprint') }}',
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
        <p class="text-sm text-gray-300 mb-2">{{ Str::limit($task->description, $descriptionLimit ?? 80) }}</p>
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
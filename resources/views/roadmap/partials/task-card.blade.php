<div class="kanban-card bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors cursor-pointer"
     @auth @if(auth()->user()->hasRole(['admin', 'staff'])) draggable="true" @endif @endauth
     data-task-id="{{ $task->id }}"
     data-order="{{ $task->order }}"
     @click="openTaskDetails({
         id: {{ $task->id }},
         title: {{ json_encode($task->title) }},
         description: {{ json_encode($task->description ?? '') }},
         status: {{ json_encode($task->status->getTitle()) }},
         status_value: {{ json_encode($task->status->value) }},
         assigned_user: {{ json_encode($task->user ? $task->user->name : 'Unassigned') }},
         assigned_user_id: {{ $task->user ? $task->user->id : 'null' }},
         assigned_user_avatar: {{ json_encode($task->user ? $task->user->avatar : '') }},
         assigned_user_initials: {{ json_encode($task->user ? $task->user->initials() : 'U') }},
         creator: {{ json_encode($task->creator ? $task->creator->name : 'Unknown') }},
         creator_avatar: {{ json_encode($task->creator ? $task->creator->avatar : '') }},
         creator_initials: {{ json_encode($task->creator ? $task->creator->initials() : 'U') }},
         created_at: {{ json_encode($task->created_at->format('M d, Y g:i A')) }},
         sprint: {{ json_encode($task->sprint ? $task->sprint->name : (isset($sprint) && is_object($sprint) ? $sprint->name : 'No sprint')) }},
         sprint_id: {{ $task->sprint_id ?? 'null' }},
         epic: {{ json_encode($task->epic ? $task->epic->name : 'No epic') }},
         epic_id: {{ $task->epic_id ?? 'null' }},
         priority: {{ json_encode(ucfirst($task->priority ?? 'medium')) }},
         priority_value: {{ json_encode($task->priority ?? 'medium') }},
         labels: {{ json_encode($task->labels->pluck('name')->toArray()) }},
         label_ids: {{ json_encode($task->labels->pluck('id')->toArray()) }},
         upvote_count: {{ $task->upvote_count ?? 0 }},
         downvote_count: {{ $task->downvote_count ?? 0 }},
         vote_score: {{ $task->vote_score ?? 0 }},
         user_vote: {{ $task->user_vote ?? 'null' }}
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
        <p class="text-sm text-gray-300 mb-2">{{ Str::limit(strip_tags($task->description), $descriptionLimit ?? 80) }}</p>
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

    </div>
</div>
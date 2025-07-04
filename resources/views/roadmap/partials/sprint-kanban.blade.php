@if($showPast)
    <div x-show="expanded" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen" x-transition:leave="transition-all ease-in duration-300" x-transition:leave-start="opacity-100 max-h-screen" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
        <div class="flex overflow-x-auto gap-6 pb-4">
@else
    <div class="flex overflow-x-auto gap-6 pb-4">
@endif
        @foreach($statuses as $status)
            @if(!in_array($status->value, ['backlog', 'ideas']))
                @if($showPast && $status->value !== 'done')
                    @continue
                @endif
                @php
                    $sprintTasks = ($tasksByStatus[$status->value] ?? collect())->where('sprint_id', $sprint->id);
                @endphp
                @include('roadmap.partials.kanban-column', [
                    'status' => $status,
                    'tasks' => $sprintTasks,
                    'sprint' => $sprint
                ])
            @endif
        @endforeach
    </div>
</div>
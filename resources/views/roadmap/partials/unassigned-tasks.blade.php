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

        <div class="flex overflow-x-auto gap-6 pb-4 max-h-100 overflow-y-auto">
            @foreach($statuses as $status)
                @if(!in_array($status->value, ['backlog', 'ideas']) && isset($tasksWithoutSprint[$status->value]) && $tasksWithoutSprint[$status->value]->count() > 0)
                    @include('roadmap.partials.kanban-column', [
                        'status' => $status,
                        'tasks' => $tasksWithoutSprint[$status->value],
                        'noSprint' => true
                    ])
                @endif
            @endforeach
        </div>
    </div>
@endif

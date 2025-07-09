<div class="kanban-column">
    <div class="bg-gray-800 rounded-lg p-4 max-h-150 overflow-y-auto">
        <h3 class="text-xl font-semibold mb-4 text-{{ $status->getColor() }}-400">
            {{ $status->getTitle() }} ({{ $tasks->count() }})
        </h3>

        <div class="space-y-3" id="column-{{ $status->value }}{{ isset($sprint) ? '-sprint-'.$sprint->id : (isset($noSprint) ? '-no-sprint' : '') }}" data-status="{{ $status->value }}" data-sprint="{{ isset($sprint) ? $sprint->id : '' }}">
            @foreach($tasks as $task)
                @include('roadmap.partials.task-card', ['task' => $task, 'descriptionLimit' => $descriptionLimit ?? 80])
            @endforeach
        </div>
    </div>
</div>

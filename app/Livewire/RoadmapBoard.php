<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\TaskStatus;
use App\Filament\Pages\Roadmap;

class RoadmapBoard extends Component
{
    public function render()
    {
        $kanban = Roadmap::make()
            ->recordTitleAttribute('title')
            ->records(Task::all())
            ->groupRecordsUsing(fn (Task $task) => $task->status->value)
            ->groups([
                TaskStatus::IDEA->value => TaskStatus::IDEA->label(),
                TaskStatus::BACKLOG->value => TaskStatus::BACKLOG->label(),
                TaskStatus::TODO->value => TaskStatus::TODO->label(),
                TaskStatus::BLOCKED->value => TaskStatus::BLOCKED->label(),
                TaskStatus::INPROGRESS->value => TaskStatus::INPROGRESS->label(),
                TaskStatus::DONE->value => TaskStatus::DONE->label(),
            ]);

        return view('livewire.roadmap-board', [
            'kanban' => $kanban,
        ]);
    }
}

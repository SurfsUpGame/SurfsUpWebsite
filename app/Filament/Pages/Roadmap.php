<?php

namespace App\Filament\Pages;

use App\TaskStatus;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use App\Models\Task;

class Roadmap extends KanbanBoard
{
    protected static string $model = Task::class;
    protected static string $statusEnum = TaskStatus::class;
}

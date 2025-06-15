<?php

namespace App;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum TaskStatus: string
{
    use IsKanbanStatus;

    case BLOCKED = 'blocked';
    case BACKLOG = 'backlog';
    case TODO = 'todo';
    case IDEA = 'idea';
    case INPROGRESS = 'in-progress';
    case DONE = 'done';

    public function label(): string
    {
        return match($this) {
            self::BLOCKED => 'Blocked',
            self::BACKLOG => 'Backlog',
            self::IDEA => 'Idea',
            self::TODO => 'To Do',
            self::INPROGRESS => 'In Progress',
            self::DONE => 'Done',
        };
    }
}

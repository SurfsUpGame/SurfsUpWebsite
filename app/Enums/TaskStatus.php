<?php

namespace App\Enums;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum TaskStatus: string
{
    use IsKanbanStatus;

    case Ideas = 'ideas';
    case Backlog = 'backlog';
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Review = 'review';
    case Done = 'done';

    public function getTitle(): string
    {
        return match ($this) {
            self::Ideas => 'Ideas',
            self::Backlog => 'Backlog',
            self::Todo => 'To Do',
            self::InProgress => 'In Progress',
            self::Review => 'Review',
            self::Done => 'Done',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Ideas => 'purple',
            self::Backlog => 'slate',
            self::Todo => 'gray',
            self::InProgress => 'primary',
            self::Review => 'warning',
            self::Done => 'success',
        };
    }
}
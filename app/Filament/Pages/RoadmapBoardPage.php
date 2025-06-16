<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Relaticle\Flowforge\Filament\Pages\KanbanBoardPage;
use Filament\Actions\Action;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;

class RoadmapBoardPage extends KanbanBoardPage
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Roadmap Board Board Page';
    protected static ?string $title = 'Roadmap';

    public function getSubject(): Builder
    {
        return Task::query();
    }

    public function mount(): void
    {
        $this
            ->titleField('title')
            ->orderField('sort_order')
            ->columnField('status')
            ->columns([
                'ideas' => 'Ideas',
                'backlog' => 'Backlog',
                'todo' => 'To Do',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
            ])
            ->columnColors([
                'ideas' => 'neutral',
                'backlog' => 'red',
                'todo' => 'blue',
                'in_progress' => 'yellow',
                'completed' => 'green',
            ])
            ->descriptionField('description')
            ->orderField('order_column')
            ->cardLabel('Task')
            ->pluralCardLabel('Tasks')
            ->cardAttributes([
                'due_date' => 'Due Date',
                'assignee.name' => 'Assigned To',
            ])
            ->cardAttributeColors([
                'due_date' => 'red',
                'assignee.name' => 'yellow',
            ])
            ->cardAttributeIcons([
                'due_date' => 'heroicon-o-calendar',
                'assignee.name' => 'heroicon-o-user',
            ]);
    }

    public function getReorderable(): bool
    {
        return Auth::check();
    }

    public function createAction(Action $action): ?Action
    {
        if (!Auth::check()) {
            return null;
        }

        return $action
            ->iconButton()
            ->icon('heroicon-o-plus')
            ->modalHeading('Create Task')
            ->modalWidth('xl')
            ->form(function (Forms\Form $form) {
                return $form->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->placeholder('Enter task title')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
                    // Add more form fields as needed
                ]);
            });
    }

    public function editAction(Action $action): ?Action
    {
        if (!Auth::check()) {
            return null;
        }


        return $action
            ->modalHeading('Edit Task')
            ->modalWidth('xl')
            ->form(function (Forms\Form $form) {
                return $form->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->placeholder('Enter task title')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'todo' => 'To Do',
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                        ])
                        ->required(),
                    // Add more form fields as needed
                ]);
            });
    }
}

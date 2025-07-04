<?php

namespace App\Filament\Admin\Resources\SprintResource\Pages;

use App\Filament\Admin\Resources\SprintResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSprint extends EditRecord
{
    protected static string $resource = SprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

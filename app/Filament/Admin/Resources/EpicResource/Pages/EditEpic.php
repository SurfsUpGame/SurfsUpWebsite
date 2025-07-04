<?php

namespace App\Filament\Admin\Resources\EpicResource\Pages;

use App\Filament\Admin\Resources\EpicResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEpic extends EditRecord
{
    protected static string $resource = EpicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

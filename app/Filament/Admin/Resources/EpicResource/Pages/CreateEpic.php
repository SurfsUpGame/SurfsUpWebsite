<?php

namespace App\Filament\Admin\Resources\EpicResource\Pages;

use App\Filament\Admin\Resources\EpicResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEpic extends CreateRecord
{
    protected static string $resource = EpicResource::class;
}

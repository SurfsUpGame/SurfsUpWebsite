<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

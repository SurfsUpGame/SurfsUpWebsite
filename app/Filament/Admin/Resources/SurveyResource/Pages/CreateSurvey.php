<?php

namespace App\Filament\Admin\Resources\SurveyResource\Pages;

use App\Filament\Admin\Resources\SurveyResource;
use Tapp\FilamentSurvey\Resources\SurveyResource\Pages\CreateSurvey as BaseCreateSurvey;

class CreateSurvey extends BaseCreateSurvey
{
    protected static string $resource = SurveyResource::class;
}

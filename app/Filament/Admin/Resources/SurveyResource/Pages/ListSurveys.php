<?php

namespace App\Filament\Admin\Resources\SurveyResource\Pages;

use App\Filament\Admin\Resources\SurveyResource;
use Tapp\FilamentSurvey\Resources\SurveyResource\Pages\ListSurveys as BaseListSurveys;

class ListSurveys extends BaseListSurveys
{
    protected static string $resource = SurveyResource::class;
}

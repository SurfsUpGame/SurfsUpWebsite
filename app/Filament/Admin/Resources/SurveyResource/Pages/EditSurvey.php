<?php

namespace App\Filament\Admin\Resources\SurveyResource\Pages;

use App\Filament\Admin\Resources\SurveyResource;
use App\Filament\Admin\Resources\SurveyResource\Pages\ViewAnswers;
use Filament\Actions;
use Tapp\FilamentSurvey\Resources\SurveyResource\Pages\EditSurvey as BaseEditSurvey;

class EditSurvey extends BaseEditSurvey
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewAnswers')
                ->label('View Answers')
                ->url(fn (): string => 'answers')
                ->icon('heroicon-o-chart-pie'),
            Actions\LocaleSwitcher::make(),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\SurveyResource\Pages;

use App\Filament\Admin\Resources\SurveyResource;
use App\Models\Survey;
use App\Filament\Admin\Widgets\SurveyAnswerOverview;
use Filament\Pages\Page;

class ViewAnswers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.resources.survey-resource.pages.view-answers';

    protected static ?string $slug = 'surveys/{record}/answers';

    public ?Survey $record = null;

    public function mount(): void
    {
        $recordId = request()->route('record');
        $this->record = Survey::findOrFail($recordId);
    }
    

    protected function getHeaderWidgets(): array
    {
        $widgets = [];

        foreach ($this->record->questions as $question) {
            if ($question->type === 'radio' || $question->type === 'multiselect') {
                $widgets[] = SurveyAnswerOverview::make(['question' => $question]);
            }
        }

        return $widgets;
    }

    public function getHeading(): string
    {
        return 'Survey Answers for: ' . ($this->record ? $this->record->name : 'N/A');
    }
}

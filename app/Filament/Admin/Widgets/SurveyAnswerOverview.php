<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Question;
use Filament\Widgets\ChartWidget;

class SurveyAnswerOverview extends ChartWidget
{
    protected static ?string $maxHeight = '300px';

    public ?Question $question = null;

    public function getHeading(): string
    {
        return 'Answers for: ' . ($this->question ? $this->question->content : 'N/A');
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        if (!$this->question) {
            return [
                'datasets' => [
                    [
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $data = [];
        $labels = [];

        if ($this->question->type === 'radio' || $this->question->type === 'multiselect') {
            $questionAnswers = $this->question->answers->groupBy('value')->map->count();

            $labels = $questionAnswers->keys()->toArray();
            $data = $questionAnswers->values()->toArray();
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }
}

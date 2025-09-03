<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SurveyResource\Pages;
use App\Filament\Admin\Widgets\SurveyAnswerOverview;
use App\Models\Survey;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Tapp\FilamentSurvey\Resources\SurveyResource as BaseSurveyResource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;

class SurveyResource extends BaseSurveyResource
{
    protected static ?string $model = Survey::class;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
            
        ];
    }

    

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::getTableColumns())
            ->actions([
                DeleteAction::make(),
                Action::make('export')
                    ->label(__('Export Answers'))
                    ->icon(config('filament-survey.actions.survey.export.icon'))
                    ->action(fn (Survey $record) => static::exportSurvey($record)),
            ])
            ->bulkActions([
                BulkAction::make('export')
                    ->label(__('Export Answers'))
                    ->icon(config('filament-survey.actions.survey.export.icon'))
                    ->action(fn (Collection $records) => static::exportSurveysBulk($records)),
            ]);
    }

    protected static function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('name')
                ->label('Name (English)')
                ->sortable()
                ->searchable(),
            \Filament\Tables\Columns\TextColumn::make('created_at')
                ->dateTime(),
            \Filament\Tables\Columns\TextColumn::make('updated_at')
                ->dateTime(),
        ];
    }

    public static function exportSurvey(Survey $survey)
    {
        $fileName = 'survey_answers_' . $survey->id . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($survey) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Question', 'Answer', 'Participant ID', 'Submitted At']);

            foreach ($survey->entries as $entry) {
                foreach ($entry->answers as $answer) {
                    fputcsv($file, [
                        $answer->question->content,
                        $answer->value,
                        $entry->participant_id,
                        $entry->created_at,
                    ]);
                }
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public static function exportSurveysBulk(Collection $surveys)
    {
        $fileName = 'surveys_answers_bulk_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($surveys) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Survey Name', 'Question', 'Answer', 'Participant ID', 'Submitted At']);

            foreach ($surveys as $survey) {
                foreach ($survey->entries as $entry) {
                    foreach ($entry->answers as $answer) {
                        fputcsv($file, [
                            $survey->name,
                            $answer->question->content,
                            $answer->value,
                            $entry->participant_id,
                            $entry->created_at,
                        ]);
                    }
                }
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

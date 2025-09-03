<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function show($surveyId)
    {
        $survey = Survey::where('id', $surveyId)->firstOrFail();
        $hasSubmitted = false;
        if (auth()->check()) {
            $hasSubmitted = $survey->entries()->where('participant_id', auth()->id())->exists();
        }
        return view('survey.show', [
            'survey' => $survey,
            'hasSubmitted' => $hasSubmitted,
        ]);
    }

    public function store(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'questions' => 'required|array',
        ]);

        $entry = $survey->entries()->create([
            'participant_id' => auth()->id(),
        ]);

        foreach ($validated['questions'] as $questionId => $answer)
        {
            $entry->answers()->create([
                'question_id' => $questionId,
                'value' => $answer,
            ]);
        }

        return redirect()->route('home')->with('success', 'Thank you for your feedback!');
    }
}

<?php

namespace App\Models;

use MattDaneshvar\Survey\Models\Survey as BaseSurvey;
use Spatie\Translatable\HasTranslations;

class Survey extends BaseSurvey
{
    use HasTranslations;

    public $translatable = ['name', 'description'];
}

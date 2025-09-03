<?php

namespace App\Models;

use MattDaneshvar\Survey\Models\Question as BaseQuestion;

class Question extends BaseQuestion
{
    protected $fillable = ['type', 'options', 'content', 'rules', 'survey_id', 'section_id'];
}

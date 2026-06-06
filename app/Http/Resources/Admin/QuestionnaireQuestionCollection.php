<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionnaireQuestionCollection extends ResourceCollection
{
    public $collects = QuestionnaireQuestionResource::class;
}

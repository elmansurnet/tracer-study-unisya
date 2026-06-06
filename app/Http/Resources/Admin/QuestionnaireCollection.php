<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionnaireCollection extends ResourceCollection
{
    public $collects = QuestionnaireResource::class;
}

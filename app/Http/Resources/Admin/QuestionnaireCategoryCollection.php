<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionnaireCategoryCollection extends ResourceCollection
{
    public $collects = QuestionnaireCategoryResource::class;
}

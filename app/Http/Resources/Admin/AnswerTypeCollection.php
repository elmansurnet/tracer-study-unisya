<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AnswerTypeCollection extends ResourceCollection
{
    public $collects = AnswerTypeResource::class;
}

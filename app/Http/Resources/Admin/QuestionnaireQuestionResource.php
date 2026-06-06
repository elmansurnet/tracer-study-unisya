<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireQuestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'questionnaire_id' => $this->questionnaire_id,
            'answer_type_id'   => $this->answer_type_id,
            'answer_type'      => $this->whenLoaded('answerType', fn () => [
                'id'     => $this->answerType?->id,
                'code'   => $this->answerType?->code,
                'name'   => $this->answerType?->name,
                'config' => $this->answerType?->config,
            ]),
            'question_text'  => $this->question_text,
            'question_order' => $this->question_order,
            'is_required'    => $this->is_required,
            'is_active'      => $this->is_active,
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
            'deleted_at'     => $this->deleted_at?->toISOString(),
        ];
    }
}

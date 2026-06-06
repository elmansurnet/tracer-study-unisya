<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                        => $this->id,
            'title'                     => $this->title,
            'description'               => $this->description,
            'respondent_type'           => $this->respondent_type,
            'scope'                     => $this->scope,
            'is_active'                 => $this->is_active,
            'version'                   => $this->version,
            'start_date'                => $this->start_date?->toDateString(),
            'end_date'                  => $this->end_date?->toDateString(),
            'questionnaire_category_id' => $this->questionnaire_category_id,
            'category'                  => $this->whenLoaded('category', fn () => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
            ]),
            'faculty_id'       => $this->faculty_id,
            'faculty'          => $this->whenLoaded('faculty', fn () => [
                'id'   => $this->faculty?->id,
                'name' => $this->faculty?->name,
            ]),
            'study_program_id' => $this->study_program_id,
            'study_program'    => $this->whenLoaded('studyProgram', fn () => [
                'id'   => $this->studyProgram?->id,
                'name' => $this->studyProgram?->name,
            ]),
            'questions_count' => $this->whenCounted('questions'),
            'questions'       => QuestionnaireQuestionResource::collection($this->whenLoaded('questions')),
            'created_by'      => $this->whenLoaded('creator', fn () => [
                'id'   => $this->creator?->id,
                'name' => $this->creator?->name,
            ]),
            'updated_by'      => $this->whenLoaded('updater', fn () => [
                'id'   => $this->updater?->id,
                'name' => $this->updater?->name,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}

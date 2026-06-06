<?php

namespace App\Http\Requests\Admin\QuestionnaireQuestion;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionnaireQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('questionnaire'));
    }

    public function rules(): array
    {
        return [
            'answer_type_id' => ['required', 'string', 'exists:answer_types,id'],
            'question_text'  => ['required', 'string'],
            'is_required'    => ['sometimes', 'boolean'],
            'is_active'      => ['sometimes', 'boolean'],
        ];
    }
}

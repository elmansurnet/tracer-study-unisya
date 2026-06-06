<?php

namespace App\Http\Requests\Admin\QuestionnaireQuestion;

use Illuminate\Foundation\Http\FormRequest;

class ReorderQuestionnaireQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('questionnaire'));
    }

    public function rules(): array
    {
        return [
            'ordered_ids'   => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['required', 'string', 'exists:questionnaire_questions,id'],
        ];
    }
}

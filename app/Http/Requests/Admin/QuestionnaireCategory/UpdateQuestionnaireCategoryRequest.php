<?php

namespace App\Http\Requests\Admin\QuestionnaireCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionnaireCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('questionnaire_category'));
    }

    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}

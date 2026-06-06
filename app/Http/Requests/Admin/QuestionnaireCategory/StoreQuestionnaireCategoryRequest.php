<?php

namespace App\Http\Requests\Admin\QuestionnaireCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionnaireCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\QuestionnaireCategory::class);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}

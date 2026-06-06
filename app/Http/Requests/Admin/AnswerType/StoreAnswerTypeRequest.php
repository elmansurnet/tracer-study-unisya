<?php

namespace App\Http\Requests\Admin\AnswerType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnswerTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\AnswerType::class);
    }

    public function rules(): array
    {
        return [
            'code'        => ['required', 'string', 'max:50', Rule::unique('answer_types', 'code')],
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'config'      => ['nullable', 'array'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}

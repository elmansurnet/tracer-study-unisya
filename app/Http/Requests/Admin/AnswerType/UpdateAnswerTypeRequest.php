<?php

namespace App\Http\Requests\Admin\AnswerType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnswerTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('answer_type'));
    }

    public function rules(): array
    {
        $id = $this->route('answer_type')?->id;

        return [
            'code'        => ['sometimes', 'required', 'string', 'max:50', Rule::unique('answer_types', 'code')->ignore($id)],
            'name'        => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'config'      => ['nullable', 'array'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}

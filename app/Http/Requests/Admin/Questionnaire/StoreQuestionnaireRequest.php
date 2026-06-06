<?php

namespace App\Http\Requests\Admin\Questionnaire;

use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Questionnaire::class);
    }

    public function rules(): array
    {
        return [
            'questionnaire_category_id' => ['required', 'string', 'exists:questionnaire_categories,id'],
            'title'                     => ['required', 'string', 'max:255'],
            'description'               => ['nullable', 'string'],
            'respondent_type'           => ['required', Rule::in(Questionnaire::RESPONDENT_TYPES)],
            'scope'                     => ['required', Rule::in(Questionnaire::SCOPES)],
            'faculty_id'                => ['nullable', 'string', 'exists:faculties,id', Rule::requiredIf(fn () => $this->scope === 'faculty')],
            'study_program_id'          => ['nullable', 'string', 'exists:study_programs,id', Rule::requiredIf(fn () => $this->scope === 'study_program')],
            'start_date'                => ['nullable', 'date'],
            'end_date'                  => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active'                 => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->scope === 'global' && ($this->faculty_id || $this->study_program_id)) {
                $validator->errors()->add('scope', 'Scope global tidak boleh memiliki faculty_id atau study_program_id.');
            }
            if ($this->scope === 'faculty' && !$this->faculty_id) {
                $validator->errors()->add('faculty_id', 'Faculty wajib diisi untuk scope faculty.');
            }
            if ($this->scope === 'study_program' && !$this->study_program_id) {
                $validator->errors()->add('study_program_id', 'Program studi wajib diisi untuk scope study_program.');
            }
        });
    }
}

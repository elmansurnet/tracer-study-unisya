<?php

namespace App\Http\Requests\Admin\Questionnaire;

use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('questionnaire'));
    }

    public function rules(): array
    {
        return [
            'questionnaire_category_id' => ['sometimes', 'required', 'string', 'exists:questionnaire_categories,id'],
            'title'                     => ['sometimes', 'required', 'string', 'max:255'],
            'description'               => ['nullable', 'string'],
            'respondent_type'           => ['sometimes', 'required', Rule::in(Questionnaire::RESPONDENT_TYPES)],
            'scope'                     => ['sometimes', 'required', Rule::in(Questionnaire::SCOPES)],
            'faculty_id'                => ['nullable', 'string', 'exists:faculties,id'],
            'study_program_id'          => ['nullable', 'string', 'exists:study_programs,id'],
            'start_date'                => ['nullable', 'date'],
            'end_date'                  => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active'                 => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $scope = $this->scope ?? $this->route('questionnaire')?->scope;
            if ($scope === 'global' && ($this->faculty_id || $this->study_program_id)) {
                $validator->errors()->add('scope', 'Scope global tidak boleh memiliki faculty_id atau study_program_id.');
            }
            if ($scope === 'faculty' && !$this->faculty_id && !$this->route('questionnaire')?->faculty_id) {
                $validator->errors()->add('faculty_id', 'Faculty wajib diisi untuk scope faculty.');
            }
            if ($scope === 'study_program' && !$this->study_program_id && !$this->route('questionnaire')?->study_program_id) {
                $validator->errors()->add('study_program_id', 'Program studi wajib diisi untuk scope study_program.');
            }
        });
    }
}

<?php

namespace App\Repositories;

use App\Models\Questionnaire;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class QuestionnaireRepository
{
    // ─── Read ──────────────────────────────────────────────────────────────────

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Questionnaire::with(['category']);

        if (isset($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['questionnaire_category_id'])) {
            $query->where('questionnaire_category_id', $filters['questionnaire_category_id']);
        }

        if (isset($filters['respondent_type'])) {
            $query->where('respondent_type', $filters['respondent_type']);
        }

        if (isset($filters['scope'])) {
            $query->where('scope', $filters['scope']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function all(bool $onlyActive = false): Collection
    {
        $query = Questionnaire::with(['category']);

        if ($onlyActive) {
            $query->active();
        }

        return $query->orderBy('title')->get();
    }

    public function findById(string $id, bool $withQuestions = false): ?Questionnaire
    {
        $query = Questionnaire::query();

        if ($withQuestions) {
            $query->with(['category', 'questions.answerType', 'faculty', 'studyProgram']);
        } else {
            $query->with(['category', 'faculty', 'studyProgram']);
        }

        return $query->find($id);
    }

    public function findByIdOrFail(string $id, bool $withQuestions = false): Questionnaire
    {
        $query = Questionnaire::query();

        if ($withQuestions) {
            $query->with(['category', 'questions.answerType', 'faculty', 'studyProgram']);
        } else {
            $query->with(['category', 'faculty', 'studyProgram']);
        }

        return $query->findOrFail($id);
    }

    // ─── Write ─────────────────────────────────────────────────────────────────

    public function create(array $data): Questionnaire
    {
        return Questionnaire::create($data);
    }

    public function update(Questionnaire $questionnaire, array $data): Questionnaire
    {
        $questionnaire->update($data);
        return $questionnaire->fresh(['category', 'faculty', 'studyProgram']);
    }

    public function delete(Questionnaire $questionnaire): void
    {
        $questionnaire->delete();
    }

    public function restore(string $id): Questionnaire
    {
        $questionnaire = Questionnaire::withTrashed()->findOrFail($id);
        $questionnaire->restore();
        return $questionnaire;
    }

    public function forceDelete(Questionnaire $questionnaire): void
    {
        $questionnaire->forceDelete();
    }
}

<?php

namespace App\Repositories;

use App\Models\QuestionnaireCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class QuestionnaireCategoryRepository
{
    // ─── Read ───────────────────────────────────────────────────────────────────

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = QuestionnaireCategory::query();

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function all(bool $onlyActive = false): Collection
    {
        $query = QuestionnaireCategory::query();

        if ($onlyActive) {
            $query->active();
        }

        return $query->orderBy('name')->get();
    }

    public function findById(string $id): ?QuestionnaireCategory
    {
        return QuestionnaireCategory::find($id);
    }

    public function findByIdOrFail(string $id): QuestionnaireCategory
    {
        return QuestionnaireCategory::findOrFail($id);
    }

    // ─── Write ─────────────────────────────────────────────────────────────────

    public function create(array $data): QuestionnaireCategory
    {
        return QuestionnaireCategory::create($data);
    }

    public function update(QuestionnaireCategory $category, array $data): QuestionnaireCategory
    {
        $category->update($data);
        return $category->fresh();
    }

    public function delete(QuestionnaireCategory $category): void
    {
        $category->delete();
    }

    public function restore(string $id): QuestionnaireCategory
    {
        $category = QuestionnaireCategory::withTrashed()->findOrFail($id);
        $category->restore();
        return $category;
    }

    public function forceDelete(QuestionnaireCategory $category): void
    {
        $category->forceDelete();
    }
}

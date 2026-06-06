<?php

namespace App\Services;

use App\Models\QuestionnaireCategory;
use App\Repositories\QuestionnaireCategoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class QuestionnaireCategoryService
{
    public function __construct(
        protected QuestionnaireCategoryRepository $repo,
        protected AuditService $auditService,
    ) {}

    // ─── Read ─────────────────────────────────────────────────────────────────

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginate($filters, $perPage);
    }

    public function all(bool $onlyActive = false): Collection
    {
        return $this->repo->all($onlyActive);
    }

    public function findById(string $id): ?QuestionnaireCategory
    {
        return $this->repo->findById($id);
    }

    public function findByIdOrFail(string $id): QuestionnaireCategory
    {
        return $this->repo->findByIdOrFail($id);
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    public function create(array $validated): QuestionnaireCategory
    {
        $data = array_merge($validated, ['created_by' => Auth::id()]);
        $category = $this->repo->create($data);

        $this->auditService->log(
            event: 'created',
            model: $category,
            newValues: $category->toArray()
        );

        return $category;
    }

    public function update(QuestionnaireCategory $category, array $validated): QuestionnaireCategory
    {
        $old = $category->toArray();
        $data = array_merge($validated, ['updated_by' => Auth::id()]);
        $category = $this->repo->update($category, $data);

        $this->auditService->log(
            event: 'updated',
            model: $category,
            oldValues: $old,
            newValues: $category->toArray()
        );

        return $category;
    }

    public function delete(QuestionnaireCategory $category): void
    {
        $this->repo->update($category, ['deleted_by' => Auth::id()]);
        $this->auditService->log(event: 'deleted', model: $category);
        $this->repo->delete($category);
    }

    public function restore(string $id): QuestionnaireCategory
    {
        $category = $this->repo->restore($id);
        $this->repo->update($category, ['deleted_by' => null, 'updated_by' => Auth::id()]);
        $this->auditService->log(event: 'restored', model: $category);
        return $category;
    }
}

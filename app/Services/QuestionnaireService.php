<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Repositories\QuestionnaireRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class QuestionnaireService
{
    public function __construct(
        protected QuestionnaireRepository $repo,
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

    public function findById(string $id, bool $withQuestions = false): ?Questionnaire
    {
        return $this->repo->findById($id, $withQuestions);
    }

    public function findByIdOrFail(string $id, bool $withQuestions = false): Questionnaire
    {
        return $this->repo->findByIdOrFail($id, $withQuestions);
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    public function create(array $validated): Questionnaire
    {
        $data = array_merge($validated, ['created_by' => Auth::id()]);
        $questionnaire = $this->repo->create($data);

        $this->auditService->log(
            event: 'created',
            model: $questionnaire,
            newValues: $questionnaire->toArray()
        );

        return $questionnaire;
    }

    public function update(Questionnaire $questionnaire, array $validated): Questionnaire
    {
        $old = $questionnaire->toArray();
        $data = array_merge($validated, ['updated_by' => Auth::id()]);
        $questionnaire = $this->repo->update($questionnaire, $data);

        $this->auditService->log(
            event: 'updated',
            model: $questionnaire,
            oldValues: $old,
            newValues: $questionnaire->toArray()
        );

        return $questionnaire;
    }

    public function delete(Questionnaire $questionnaire): void
    {
        $this->repo->update($questionnaire, ['deleted_by' => Auth::id()]);
        $this->auditService->log(event: 'deleted', model: $questionnaire);
        $this->repo->delete($questionnaire);
    }

    public function restore(string $id): Questionnaire
    {
        $questionnaire = $this->repo->restore($id);
        $this->repo->update($questionnaire, ['deleted_by' => null, 'updated_by' => Auth::id()]);
        $this->auditService->log(event: 'restored', model: $questionnaire);
        return $questionnaire;
    }
}

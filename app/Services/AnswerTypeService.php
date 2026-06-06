<?php

namespace App\Services;

use App\Models\AnswerType;
use App\Repositories\AnswerTypeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class AnswerTypeService
{
    public function __construct(
        protected AnswerTypeRepository $repo,
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

    public function findById(string $id): ?AnswerType
    {
        return $this->repo->findById($id);
    }

    public function findByIdOrFail(string $id): AnswerType
    {
        return $this->repo->findByIdOrFail($id);
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    public function create(array $validated): AnswerType
    {
        $data = array_merge($validated, ['created_by' => Auth::id()]);
        $answerType = $this->repo->create($data);

        $this->auditService->log(
            event: 'created',
            model: $answerType,
            newValues: $answerType->toArray()
        );

        return $answerType;
    }

    public function update(AnswerType $answerType, array $validated): AnswerType
    {
        $old = $answerType->toArray();
        $data = array_merge($validated, ['updated_by' => Auth::id()]);
        $answerType = $this->repo->update($answerType, $data);

        $this->auditService->log(
            event: 'updated',
            model: $answerType,
            oldValues: $old,
            newValues: $answerType->toArray()
        );

        return $answerType;
    }

    public function delete(AnswerType $answerType): void
    {
        $this->auditService->log(event: 'deleted', model: $answerType);
        $this->repo->delete($answerType);
    }
}

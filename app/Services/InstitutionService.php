<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\User;
use App\Repositories\InstitutionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class InstitutionService
{
    public function __construct(
        protected InstitutionRepository $repository,
        protected AuditService $auditService
    ) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->repository->paginate($perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): Institution
    {
        $institution = $this->repository->findById($id);
        if (! $institution) {
            abort(404, 'Institusi tidak ditemukan.');
        }
        return $institution;
    }

    public function create(array $validated, User $actor): Institution
    {
        $institution = $this->repository->create([
            'id'         => Str::ulid(),
            'name'       => $validated['name'],
            'type'       => $validated['type'],
            'sector'     => $validated['sector'] ?? null,
            'website'    => $validated['website'] ?? null,
            'is_active'  => $validated['is_active'] ?? 1,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);

        if (! empty($validated['detail'])) {
            $this->repository->upsertDetail($institution, array_merge(
                $validated['detail'],
                ['created_by' => $actor->id, 'updated_by' => $actor->id]
            ));
        }

        $this->auditService->log(
            event: 'created',
            auditable: $institution,
            newValues: $institution->load('detail')->toArray(),
            actor: $actor
        );

        return $institution->load('detail');
    }

    public function update(Institution $institution, array $validated, User $actor): Institution
    {
        $oldValues = $institution->toArray();

        $updateData = array_filter([
            'name'       => $validated['name'] ?? null,
            'type'       => $validated['type'] ?? null,
            'sector'     => array_key_exists('sector', $validated) ? $validated['sector'] : null,
            'website'    => array_key_exists('website', $validated) ? $validated['website'] : null,
            'is_active'  => $validated['is_active'] ?? null,
            'updated_by' => $actor->id,
        ], fn ($v) => $v !== null);

        $updated = $this->repository->update($institution, $updateData);

        if (isset($validated['detail'])) {
            $this->repository->upsertDetail($updated, array_merge(
                $validated['detail'],
                ['updated_by' => $actor->id]
            ));
        }

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->load('detail')->toArray(),
            actor: $actor
        );

        return $updated->load('detail');
    }

    public function updateDetail(Institution $institution, array $validated, User $actor): Institution
    {
        $this->repository->upsertDetail($institution, array_merge(
            $validated,
            ['updated_by' => $actor->id]
        ));

        $this->auditService->log(
            event: 'updated',
            auditable: $institution,
            oldValues: [],
            newValues: $institution->load('detail')->toArray(),
            actor: $actor
        );

        return $institution->load('detail');
    }

    public function delete(Institution $institution, User $actor): void
    {
        $this->auditService->log(
            event: 'deleted',
            auditable: $institution,
            oldValues: $institution->toArray(),
            actor: $actor
        );

        $this->repository->softDelete($institution, $actor->id);
    }

    public function restore(string $id, User $actor): Institution
    {
        $institution = $this->repository->restore($id);
        if (! $institution) {
            abort(404, 'Institusi tidak ditemukan atau sudah aktif.');
        }

        $this->auditService->log(
            event: 'restored',
            auditable: $institution,
            newValues: $institution->toArray(),
            actor: $actor
        );

        return $institution->load('detail');
    }

    public function allActive(): \Illuminate\Support\Collection
    {
        return $this->repository->allActive();
    }
}

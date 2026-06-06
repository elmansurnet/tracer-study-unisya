<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\User;
use App\Repositories\InstitutionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InstitutionService
{
    public function __construct(
        protected InstitutionRepository $institutionRepository,
        protected AuditService $auditService
    ) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->institutionRepository->paginate($perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): Institution
    {
        $institution = $this->institutionRepository->findById($id);
        if (! $institution) {
            abort(404, 'Institusi tidak ditemukan.');
        }
        return $institution;
    }

    public function create(array $validated, User $actor): Institution
    {
        $existing = $this->institutionRepository->findByName($validated['name']);
        if ($existing) {
            throw ValidationException::withMessages([
                'name' => ['Nama institusi sudah terdaftar.'],
            ]);
        }

        $institution = $this->institutionRepository->create([
            'id'         => Str::ulid(),
            'name'       => $validated['name'],
            'type'       => $validated['type'],
            'sector'     => $validated['sector'] ?? null,
            'website'    => $validated['website'] ?? null,
            'logo'       => $validated['logo'] ?? null,
            'is_active'  => $validated['is_active'] ?? 1,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);

        $this->auditService->log(
            event: 'created',
            auditable: $institution,
            newValues: $institution->toArray(),
            actor: $actor
        );

        return $institution;
    }

    public function update(Institution $institution, array $validated, User $actor): Institution
    {
        if (isset($validated['name'])) {
            $conflict = $this->institutionRepository->findByName($validated['name'], $institution->id);
            if ($conflict) {
                throw ValidationException::withMessages([
                    'name' => ['Nama institusi sudah terdaftar.'],
                ]);
            }
        }

        $oldValues = $institution->toArray();

        $updateData = array_filter([
            'name'       => $validated['name'] ?? null,
            'type'       => $validated['type'] ?? null,
            'sector'     => array_key_exists('sector', $validated) ? $validated['sector'] : null,
            'website'    => array_key_exists('website', $validated) ? $validated['website'] : null,
            'logo'       => array_key_exists('logo', $validated) ? $validated['logo'] : null,
            'is_active'  => $validated['is_active'] ?? null,
            'updated_by' => $actor->id,
        ], fn ($v) => $v !== null);

        $updated = $this->institutionRepository->update($institution, $updateData);

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->toArray(),
            actor: $actor
        );

        return $updated;
    }

    public function delete(Institution $institution, User $actor): void
    {
        $this->auditService->log(
            event: 'deleted',
            auditable: $institution,
            oldValues: $institution->toArray(),
            actor: $actor
        );

        $this->institutionRepository->softDelete($institution, $actor->id);
    }

    public function restore(string $id, User $actor): Institution
    {
        $institution = $this->institutionRepository->restore($id);
        if (! $institution) {
            abort(404, 'Institusi tidak ditemukan atau sudah aktif.');
        }

        $this->auditService->log(
            event: 'restored',
            auditable: $institution,
            newValues: $institution->toArray(),
            actor: $actor
        );

        return $institution;
    }

    public function allActive(): \Illuminate\Support\Collection
    {
        return $this->institutionRepository->allActive();
    }
}

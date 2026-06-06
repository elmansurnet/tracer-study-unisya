<?php

namespace App\Repositories;

use App\Models\Institution;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InstitutionRepository
{
    public function __construct(protected Institution $model) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->when(isset($filters['search']), fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('name', 'like', "%{$filters['search']}%")
                       ->orWhere('sector', 'like', "%{$filters['search']}%")
                ))
            ->when(isset($filters['is_active']), fn ($q) =>
                $q->where('is_active', $filters['is_active']))
            ->when(isset($filters['type']), fn ($q) =>
                $q->where('type', $filters['type']))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function findById(string $id): ?Institution
    {
        return $this->model->with('detail')->find($id);
    }

    public function findByName(string $name, ?string $exceptId = null): ?Institution
    {
        return $this->model
            ->where('name', $name)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->first();
    }

    public function create(array $data): Institution
    {
        return $this->model->create($data);
    }

    public function update(Institution $institution, array $data): Institution
    {
        $institution->update($data);
        return $institution->fresh(['detail']);
    }

    public function softDelete(Institution $institution, string $deletedBy): bool
    {
        $institution->deleted_by = $deletedBy;
        $institution->save();
        return (bool) $institution->delete();
    }

    public function restore(string $id): ?Institution
    {
        $institution = $this->model->withTrashed()->find($id);
        if ($institution) {
            $institution->deleted_by = null;
            $institution->save();
            $institution->restore();
        }
        return $institution;
    }

    public function allActive(): Collection
    {
        return $this->model
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }
}

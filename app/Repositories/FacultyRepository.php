<?php

namespace App\Repositories;

use App\Models\Faculty;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FacultyRepository
{
    public function __construct(protected Faculty $model) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->withCount('studyPrograms')
            ->when(isset($filters['search']), fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('name', 'like', "%{$filters['search']}%")
                       ->orWhere('code', 'like', "%{$filters['search']}%")
                ))
            ->when(isset($filters['is_active']), fn ($q) =>
                $q->where('is_active', $filters['is_active']))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function findById(string $id): ?Faculty
    {
        return $this->model->withCount('studyPrograms')->find($id);
    }

    public function findByCode(string $code, ?string $exceptId = null): ?Faculty
    {
        return $this->model
            ->where('code', $code)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->first();
    }

    public function create(array $data): Faculty
    {
        return $this->model->create($data);
    }

    public function update(Faculty $faculty, array $data): Faculty
    {
        $faculty->update($data);
        return $faculty->fresh();
    }

    public function softDelete(Faculty $faculty, string $deletedBy): bool
    {
        $faculty->deleted_by = $deletedBy;
        $faculty->save();
        return (bool) $faculty->delete();
    }

    public function restore(string $id): ?Faculty
    {
        $faculty = $this->model->withTrashed()->find($id);
        if ($faculty) {
            $faculty->deleted_by = null;
            $faculty->save();
            $faculty->restore();
        }
        return $faculty;
    }

    public function allActive(): Collection
    {
        return $this->model
            ->where('is_active', 1)
            ->withCount('studyPrograms')
            ->orderBy('name')
            ->get();
    }
}

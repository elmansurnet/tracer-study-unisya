<?php

namespace App\Repositories;

use App\Models\StudyProgram;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StudyProgramRepository
{
    public function __construct(protected StudyProgram $model) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->with('faculty:id,name,code')
            ->when(isset($filters['search']), fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('name', 'like', "%{$filters['search']}%")
                       ->orWhere('code', 'like', "%{$filters['search']}%")
                ))
            ->when(isset($filters['faculty_id']), fn ($q) =>
                $q->where('faculty_id', $filters['faculty_id']))
            ->when(isset($filters['degree_level']), fn ($q) =>
                $q->where('degree_level', $filters['degree_level']))
            ->when(isset($filters['is_active']), fn ($q) =>
                $q->where('is_active', $filters['is_active']))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function findById(string $id): ?StudyProgram
    {
        return $this->model->with('faculty:id,name,code')->find($id);
    }

    public function findByCode(string $code, ?string $exceptId = null): ?StudyProgram
    {
        return $this->model
            ->where('code', $code)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->first();
    }

    public function byFaculty(string $facultyId): Collection
    {
        return $this->model
            ->where('faculty_id', $facultyId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): StudyProgram
    {
        return $this->model->create($data);
    }

    public function update(StudyProgram $studyProgram, array $data): StudyProgram
    {
        $studyProgram->update($data);
        return $studyProgram->fresh();
    }

    public function softDelete(StudyProgram $studyProgram, string $deletedBy): bool
    {
        $studyProgram->deleted_by = $deletedBy;
        $studyProgram->save();
        return (bool) $studyProgram->delete();
    }

    public function restore(string $id): ?StudyProgram
    {
        $sp = $this->model->withTrashed()->find($id);
        if ($sp) {
            $sp->deleted_by = null;
            $sp->save();
            $sp->restore();
        }
        return $sp;
    }

    public function allActive(): Collection
    {
        return $this->model
            ->with('faculty:id,name,code')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }
}

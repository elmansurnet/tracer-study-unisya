<?php

namespace App\Repositories;

use App\Models\Alumni;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AlumniRepository
{
    public function __construct(protected Alumni $model) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->with([
                'studyProgram:id,name,code,faculty_id',
                'studyProgram.faculty:id,name,code',
            ])
            ->when(isset($filters['search']), fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('name', 'like', "%{$filters['search']}%")
                       ->orWhere('nim', 'like', "%{$filters['search']}%")
                       ->orWhere('email', 'like', "%{$filters['search']}%")
                ))
            ->when(isset($filters['study_program_id']), fn ($q) =>
                $q->where('study_program_id', $filters['study_program_id']))
            ->when(isset($filters['graduation_year']), fn ($q) =>
                $q->where('graduation_year', $filters['graduation_year']))
            ->when(isset($filters['employment_status']), fn ($q) =>
                $q->where('employment_status', $filters['employment_status']))
            ->when(isset($filters['gender']), fn ($q) =>
                $q->where('gender', $filters['gender']))
            ->when(isset($filters['is_employed']), fn ($q) =>
                $q->where('is_employed', $filters['is_employed']))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function findById(string $id): ?Alumni
    {
        return $this->model
            ->with([
                'studyProgram:id,name,code,faculty_id',
                'studyProgram.faculty:id,name,code',
                'user:id,name,email',
                'employmentHistories' => fn ($q) => $q->orderBy('start_date', 'desc'),
                'employmentHistories.institution:id,name',
                'employmentHistories.profession:id,name',
            ])
            ->find($id);
    }

    public function findByNim(string $nim, ?string $exceptId = null): ?Alumni
    {
        return $this->model
            ->where('nim', $nim)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->first();
    }

    public function findByUserId(string $userId): ?Alumni
    {
        return $this->model
            ->where('user_id', $userId)
            ->with([
                'studyProgram:id,name,code,faculty_id',
                'studyProgram.faculty:id,name,code',
            ])
            ->first();
    }

    public function create(array $data): Alumni
    {
        return $this->model->create($data);
    }

    public function update(Alumni $alumni, array $data): Alumni
    {
        $alumni->update($data);
        return $alumni->fresh();
    }

    public function softDelete(Alumni $alumni, string $deletedBy): bool
    {
        $alumni->deleted_by = $deletedBy;
        $alumni->save();
        return (bool) $alumni->delete();
    }

    public function restore(string $id): ?Alumni
    {
        $alumni = $this->model->withTrashed()->find($id);
        if ($alumni) {
            $alumni->deleted_by = null;
            $alumni->save();
            $alumni->restore();
        }
        return $alumni;
    }

    public function byStudyProgram(string $studyProgramId, ?int $graduationYear = null): Collection
    {
        return $this->model
            ->where('study_program_id', $studyProgramId)
            ->when($graduationYear, fn ($q) => $q->where('graduation_year', $graduationYear))
            ->orderBy('name')
            ->get();
    }

    public function byGraduationYear(int $year): Collection
    {
        return $this->model
            ->where('graduation_year', $year)
            ->with('studyProgram:id,name,code')
            ->orderBy('name')
            ->get();
    }

    /** @return array<string, int> keyed by employment_status */
    public function countByEmploymentStatus(?int $graduationYear = null): array
    {
        return $this->model
            ->newQuery()
            ->when($graduationYear, fn ($q) => $q->where('graduation_year', $graduationYear))
            ->whereNotNull('employment_status')
            ->selectRaw('employment_status, COUNT(*) as total')
            ->groupBy('employment_status')
            ->pluck('total', 'employment_status')
            ->toArray();
    }

    public function graduationYears(): Collection
    {
        return $this->model
            ->selectRaw('DISTINCT graduation_year')
            ->orderBy('graduation_year', 'desc')
            ->pluck('graduation_year');
    }
}

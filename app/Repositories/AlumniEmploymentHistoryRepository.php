<?php

namespace App\Repositories;

use App\Models\AlumniEmploymentHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AlumniEmploymentHistoryRepository
{
    public function __construct(protected AlumniEmploymentHistory $model) {}

    public function paginate(
        string $alumniId,
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'start_date',
        string $sortDir = 'desc'
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->where('alumni_id', $alumniId)
            ->with([
                'institution:id,name,type',
                'profession:id,name,category_id',
                'profession.category:id,name',
            ])
            ->when(isset($filters['is_current']), fn ($q) =>
                $q->where('is_current', $filters['is_current']))
            ->when(isset($filters['institution_id']), fn ($q) =>
                $q->where('institution_id', $filters['institution_id']))
            ->when(isset($filters['profession_id']), fn ($q) =>
                $q->where('profession_id', $filters['profession_id']))
            ->when(isset($filters['job_relevance']), fn ($q) =>
                $q->where('job_relevance', $filters['job_relevance']))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function findById(string $id): ?AlumniEmploymentHistory
    {
        return $this->model
            ->with([
                'institution:id,name,type',
                'profession:id,name,category_id',
                'profession.category:id,name',
                'alumni:id,name,nim',
            ])
            ->find($id);
    }

    public function currentForAlumni(string $alumniId): ?AlumniEmploymentHistory
    {
        return $this->model
            ->where('alumni_id', $alumniId)
            ->where('is_current', true)
            ->with([
                'institution:id,name,type',
                'profession:id,name',
            ])
            ->first();
    }

    public function byAlumni(string $alumniId): Collection
    {
        return $this->model
            ->where('alumni_id', $alumniId)
            ->with([
                'institution:id,name,type',
                'profession:id,name',
            ])
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function create(array $data): AlumniEmploymentHistory
    {
        return $this->model->create($data);
    }

    public function update(AlumniEmploymentHistory $history, array $data): AlumniEmploymentHistory
    {
        $history->update($data);
        return $history->fresh();
    }

    public function softDelete(AlumniEmploymentHistory $history, string $deletedBy): bool
    {
        $history->deleted_by = $deletedBy;
        $history->save();
        return (bool) $history->delete();
    }

    public function restore(string $id): ?AlumniEmploymentHistory
    {
        $history = $this->model->withTrashed()->find($id);
        if ($history) {
            $history->deleted_by = null;
            $history->save();
            $history->restore();
        }
        return $history;
    }

    /**
     * Unset is_current for all other histories of the same alumni
     * when a new current job is being set.
     */
    public function clearCurrentForAlumni(string $alumniId, ?string $exceptId = null): void
    {
        $this->model
            ->where('alumni_id', $alumniId)
            ->where('is_current', true)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->update(['is_current' => false]);
    }
}

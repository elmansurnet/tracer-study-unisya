<?php

namespace App\Repositories;

use App\Models\AlumniRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AlumniRequestRepository
{
    public function __construct(protected AlumniRequest $model) {}

    /**
     * Paginate dengan filter — digunakan admin dan alumni.
     */
    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortDir = 'desc'
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->with([
                'alumni:id,name,nim,study_program_id',
                'alumni.studyProgram:id,name,code',
                'reviewer:id,name',
            ])
            ->when(isset($filters['alumni_id']), fn ($q) =>
                $q->where('alumni_id', $filters['alumni_id']))
            ->when(isset($filters['status']), fn ($q) =>
                $q->where('status', $filters['status']))
            ->when(isset($filters['type']), fn ($q) =>
                $q->where('type', $filters['type']))
            ->when(isset($filters['search']), fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('field_name', 'like', "%{$filters['search']}%")
                       ->orWhere('new_value', 'like', "%{$filters['search']}%")
                       ->orWhereHas('alumni', fn ($qa) =>
                           $qa->where('name', 'like', "%{$filters['search']}%")
                              ->orWhere('nim', 'like', "%{$filters['search']}%")
                       )
                ))
            ->when(isset($filters['study_program_id']), fn ($q) =>
                $q->whereHas('alumni', fn ($qa) =>
                    $qa->where('study_program_id', $filters['study_program_id'])
                ))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    /**
     * Ambil satu permohonan beserta relasi lengkap.
     */
    public function findById(string $id): ?AlumniRequest
    {
        return $this->model
            ->with([
                'alumni:id,name,nim,study_program_id,email,phone',
                'alumni.studyProgram:id,name,code,faculty_id',
                'alumni.studyProgram.faculty:id,name,code',
                'reviewer:id,name,email',
                'creator:id,name',
                'updater:id,name',
            ])
            ->find($id);
    }

    /**
     * Semua permohonan satu alumni — untuk halaman profil alumni.
     */
    public function findByAlumniId(string $alumniId): Collection
    {
        return $this->model
            ->where('alumni_id', $alumniId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Hitung permohonan menunggu (untuk badge notifikasi admin).
     */
    public function countPending(?string $alumniId = null): int
    {
        return $this->model
            ->newQuery()
            ->where('status', AlumniRequest::STATUS_MENUNGGU)
            ->when($alumniId, fn ($q) => $q->where('alumni_id', $alumniId))
            ->count();
    }

    /**
     * Statistik per-status — untuk dashboard admin.
     */
    public function countByStatus(): array
    {
        return $this->model
            ->newQuery()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    public function create(array $data): AlumniRequest
    {
        return $this->model->create($data);
    }

    public function update(AlumniRequest $request, array $data): AlumniRequest
    {
        $request->update($data);
        return $request->fresh();
    }

    /**
     * Setujui permohonan — set status disetujui + audit reviewer.
     */
    public function approve(AlumniRequest $request, string $reviewedBy, ?string $notes = null): AlumniRequest
    {
        $request->update([
            'status'       => AlumniRequest::STATUS_DISETUJUI,
            'reviewed_by'  => $reviewedBy,
            'reviewed_at'  => now(),
            'review_notes' => $notes,
            'updated_by'   => $reviewedBy,
        ]);
        return $request->fresh();
    }

    /**
     * Tolak permohonan — set status ditolak + audit reviewer.
     */
    public function reject(AlumniRequest $request, string $reviewedBy, ?string $notes = null): AlumniRequest
    {
        $request->update([
            'status'       => AlumniRequest::STATUS_DITOLAK,
            'reviewed_by'  => $reviewedBy,
            'reviewed_at'  => now(),
            'review_notes' => $notes,
            'updated_by'   => $reviewedBy,
        ]);
        return $request->fresh();
    }

    public function softDelete(AlumniRequest $request, string $deletedBy): bool
    {
        $request->deleted_by = $deletedBy;
        $request->save();
        return (bool) $request->delete();
    }

    public function restore(string $id): ?AlumniRequest
    {
        $request = $this->model->withTrashed()->find($id);
        if ($request) {
            $request->deleted_by = null;
            $request->save();
            $request->restore();
        }
        return $request;
    }
}

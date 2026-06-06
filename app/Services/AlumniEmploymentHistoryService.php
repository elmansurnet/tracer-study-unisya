<?php

namespace App\Services;

use App\Models\Alumni;
use App\Models\AlumniEmploymentHistory;
use App\Models\User;
use App\Repositories\AlumniEmploymentHistoryRepository;
use App\Repositories\AlumniRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AlumniEmploymentHistoryService
{
    public function __construct(
        protected AlumniEmploymentHistoryRepository $historyRepository,
        protected AlumniRepository                  $alumniRepository,
        protected AuditService                      $auditService
    ) {}

    // ─── Read ──────────────────────────────────────────────────────────────

    public function paginate(
        string $alumniId,
        int    $perPage = 15,
        array  $filters = [],
        string $sortBy  = 'start_date',
        string $sortDir = 'desc'
    ): LengthAwarePaginator {
        return $this->historyRepository->paginate($alumniId, $perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): AlumniEmploymentHistory
    {
        $history = $this->historyRepository->findById($id);
        if (! $history) {
            abort(404, 'Riwayat pekerjaan tidak ditemukan.');
        }
        return $history;
    }

    public function currentForAlumni(string $alumniId): ?AlumniEmploymentHistory
    {
        return $this->historyRepository->currentForAlumni($alumniId);
    }

    public function byAlumni(string $alumniId): Collection
    {
        return $this->historyRepository->byAlumni($alumniId);
    }

    // ─── Write ────────────────────────────────────────────────────────────

    public function create(array $validated, Alumni $alumni, User $actor): AlumniEmploymentHistory
    {
        // Business rule: hanya satu pekerjaan current per alumni
        $isCurrent = (bool) ($validated['is_current'] ?? false);
        if ($isCurrent) {
            $this->historyRepository->clearCurrentForAlumni($alumni->id);
        }

        $history = $this->historyRepository->create([
            'id'             => Str::ulid(),
            'alumni_id'      => $alumni->id,
            'institution_id' => $validated['institution_id'] ?? null,
            'profession_id'  => $validated['profession_id']  ?? null,
            'job_title'      => $validated['job_title']      ?? null,
            'start_date'     => $validated['start_date'],
            'end_date'       => $validated['end_date']       ?? null,
            'is_current'     => $isCurrent,
            'salary_range'   => $validated['salary_range']   ?? null,
            'job_relevance'  => $validated['job_relevance']  ?? null,
            'notes'          => $validated['notes']          ?? null,
            'created_by'     => $actor->id,
            'updated_by'     => $actor->id,
        ]);

        // Sync is_employed + employment_status ke tabel alumni
        $this->syncAlumniEmploymentStatus($alumni, $history, $actor);

        $this->auditService->log(
            event: 'created',
            auditable: $history,
            newValues: $history->toArray(),
            actor: $actor
        );

        return $history;
    }

    public function update(
        AlumniEmploymentHistory $history,
        array                   $validated,
        User                    $actor
    ): AlumniEmploymentHistory {
        // Business rule: is_current conflict
        $isCurrent = $validated['is_current'] ?? $history->is_current;
        if ($isCurrent && ! $history->is_current) {
            // Menjadi current baru → hapus flag current lain
            $this->historyRepository->clearCurrentForAlumni($history->alumni_id, $history->id);
        }

        $oldValues = $history->toArray();

        $updateData = [];
        $scalarFields = [
            'institution_id', 'profession_id', 'job_title',
            'start_date', 'end_date', 'is_current',
            'salary_range', 'job_relevance', 'notes',
        ];

        foreach ($scalarFields as $field) {
            if (array_key_exists($field, $validated)) {
                $updateData[$field] = $validated[$field];
            }
        }

        $updateData['updated_by'] = $actor->id;

        $updated = $this->historyRepository->update($history, $updateData);

        // Re-sync is_employed pada alumni jika is_current berubah
        if (array_key_exists('is_current', $validated)) {
            $alumni = $this->alumniRepository->findById($history->alumni_id);
            if ($alumni) {
                $this->syncAlumniEmploymentStatus($alumni, $updated, $actor);
            }
        }

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->toArray(),
            actor: $actor
        );

        return $updated;
    }

    public function delete(AlumniEmploymentHistory $history, User $actor): void
    {
        $alumniId     = $history->alumni_id;
        $wasCurrent   = $history->is_current;

        $this->auditService->log(
            event: 'deleted',
            auditable: $history,
            oldValues: $history->toArray(),
            actor: $actor
        );

        $this->historyRepository->softDelete($history, $actor->id);

        // Jika yang dihapus adalah pekerjaan current, update is_employed Alumni
        if ($wasCurrent) {
            $alumni = $this->alumniRepository->findById($alumniId);
            if ($alumni) {
                $hasOtherCurrent = $this->historyRepository->currentForAlumni($alumniId);
                $this->alumniRepository->update($alumni, [
                    'is_employed' => (bool) $hasOtherCurrent,
                    'updated_by'  => $actor->id,
                ]);
            }
        }
    }

    public function restore(string $id, User $actor): AlumniEmploymentHistory
    {
        $history = $this->historyRepository->restore($id);
        if (! $history) {
            abort(404, 'Riwayat pekerjaan tidak ditemukan atau sudah aktif.');
        }

        $this->auditService->log(
            event: 'restored',
            auditable: $history,
            newValues: $history->toArray(),
            actor: $actor
        );

        // Sync is_employed jika yang di-restore adalah current
        if ($history->is_current) {
            $alumni = $this->alumniRepository->findById($history->alumni_id);
            if ($alumni) {
                $this->alumniRepository->update($alumni, [
                    'is_employed' => true,
                    'updated_by'  => $actor->id,
                ]);
            }
        }

        return $history;
    }

    // ─── Internal business-rule helpers ───────────────────────────────────────

    /**
     * Sync kolom is_employed pada Alumni berdasarkan status pekerjaan terkini.
     * Dipanggil setiap kali riwayat pekerjaan dibuat / diupdate / dihapus.
     */
    private function syncAlumniEmploymentStatus(
        Alumni                  $alumni,
        AlumniEmploymentHistory $triggerHistory,
        User                    $actor
    ): void {
        // Pekerjaan current yang baru saja di-set → alumni is_employed = true
        if ($triggerHistory->is_current) {
            $this->alumniRepository->update($alumni, [
                'is_employed' => true,
                'updated_by'  => $actor->id,
            ]);
            return;
        }

        // Tidak ada current → cek apakah masih ada current lain
        $hasCurrent = $this->historyRepository->currentForAlumni($alumni->id);
        $this->alumniRepository->update($alumni, [
            'is_employed' => (bool) $hasCurrent,
            'updated_by'  => $actor->id,
        ]);
    }
}

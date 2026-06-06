<?php

namespace App\Services;

use App\Models\AlumniRequest;
use App\Models\User;
use App\Repositories\AlumniRepository;
use App\Repositories\AlumniRequestRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AlumniRequestService
{
    public function __construct(
        protected AlumniRequestRepository $requestRepository,
        protected AlumniRepository        $alumniRepository,
        protected AuditService            $auditService
    ) {}

    // ─── Read ─────────────────────────────────────────────────────────────────

    public function paginate(
        int    $perPage = 15,
        array  $filters = [],
        string $sortBy  = 'created_at',
        string $sortDir = 'desc'
    ): LengthAwarePaginator {
        return $this->requestRepository->paginate($perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): AlumniRequest
    {
        $request = $this->requestRepository->findById($id);
        if (! $request) {
            abort(404, 'Permohonan tidak ditemukan.');
        }
        return $request;
    }

    public function countPending(?string $alumniId = null): int
    {
        return $this->requestRepository->countPending($alumniId);
    }

    public function countByStatus(): array
    {
        return $this->requestRepository->countByStatus();
    }

    // ─── Write ───────────────────────────────────────────────────────────────

    /**
     * Alumni mengajukan permohonan perubahan data.
     * Validasi: alumni harus exist + tidak boleh ada permohonan pending
     * untuk field_name yang sama.
     */
    public function create(array $validated, User $actor): AlumniRequest
    {
        // 1. Pastikan alumni exist
        $alumni = $this->alumniRepository->findById($validated['alumni_id']);
        if (! $alumni) {
            throw ValidationException::withMessages([
                'alumni_id' => ['Alumni tidak ditemukan.'],
            ]);
        }

        // 2. Cegah duplikat pending untuk field_name yang sama
        $existingPending = AlumniRequest::query()
            ->where('alumni_id', $validated['alumni_id'])
            ->where('field_name', $validated['field_name'])
            ->where('status', AlumniRequest::STATUS_MENUNGGU)
            ->exists();

        if ($existingPending) {
            throw ValidationException::withMessages([
                'field_name' => ['Sudah ada permohonan yang menunggu persetujuan untuk field ini.'],
            ]);
        }

        $alumniRequest = $this->requestRepository->create([
            'id'         => Str::ulid(),
            'alumni_id'  => $validated['alumni_id'],
            'type'       => $validated['type'],
            'field_name' => $validated['field_name'],
            'old_value'  => $validated['old_value']  ?? null,
            'new_value'  => $validated['new_value'],
            'reason'     => $validated['reason']      ?? null,
            'status'     => AlumniRequest::STATUS_MENUNGGU,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);

        $this->auditService->log(
            event: 'created',
            auditable: $alumniRequest,
            newValues: $alumniRequest->toArray(),
            actor: $actor
        );

        return $alumniRequest;
    }

    /**
     * Admin menyetujui permohonan.
     * Jika type update_akademik atau update_profil, terapkan perubahan ke tabel alumni.
     */
    public function approve(
        AlumniRequest $alumniRequest,
        User $actor,
        ?string $notes = null
    ): AlumniRequest {
        if (! $alumniRequest->isPending()) {
            throw ValidationException::withMessages([
                'status' => ['Hanya permohonan berstatus menunggu yang dapat disetujui.'],
            ]);
        }

        $oldValues = $alumniRequest->toArray();

        // Terapkan perubahan ke data alumni jika permohonan tipe data
        $applyableTypes = [
            AlumniRequest::TYPE_UPDATE_AKADEMIK,
            AlumniRequest::TYPE_UPDATE_PROFIL,
        ];

        if (in_array($alumniRequest->type, $applyableTypes, true)) {
            $alumni = $this->alumniRepository->findById($alumniRequest->alumni_id);
            if ($alumni) {
                $this->alumniRepository->update($alumni, [
                    $alumniRequest->field_name => $alumniRequest->new_value,
                    'updated_by'               => $actor->id,
                ]);
            }
        }

        $approved = $this->requestRepository->approve($alumniRequest, $actor->id, $notes);

        $this->auditService->log(
            event: 'approved',
            auditable: $approved,
            oldValues: $oldValues,
            newValues: $approved->toArray(),
            actor: $actor
        );

        return $approved;
    }

    /**
     * Admin menolak permohonan dengan alasan.
     */
    public function reject(
        AlumniRequest $alumniRequest,
        User $actor,
        ?string $notes = null
    ): AlumniRequest {
        if (! $alumniRequest->isPending()) {
            throw ValidationException::withMessages([
                'status' => ['Hanya permohonan berstatus menunggu yang dapat ditolak.'],
            ]);
        }

        $oldValues = $alumniRequest->toArray();

        $rejected = $this->requestRepository->reject($alumniRequest, $actor->id, $notes);

        $this->auditService->log(
            event: 'rejected',
            auditable: $rejected,
            oldValues: $oldValues,
            newValues: $rejected->toArray(),
            actor: $actor
        );

        return $rejected;
    }

    public function delete(AlumniRequest $alumniRequest, User $actor): void
    {
        // Hanya permohonan menunggu atau ditolak yang dapat dihapus
        if ($alumniRequest->isApproved()) {
            abort(422, 'Permohonan yang sudah disetujui tidak dapat dihapus.');
        }

        $this->auditService->log(
            event: 'deleted',
            auditable: $alumniRequest,
            oldValues: $alumniRequest->toArray(),
            actor: $actor
        );

        $this->requestRepository->softDelete($alumniRequest, $actor->id);
    }

    public function restore(string $id, User $actor): AlumniRequest
    {
        $alumniRequest = $this->requestRepository->restore($id);
        if (! $alumniRequest) {
            abort(404, 'Permohonan tidak ditemukan atau sudah aktif.');
        }

        $this->auditService->log(
            event: 'restored',
            auditable: $alumniRequest,
            newValues: $alumniRequest->toArray(),
            actor: $actor
        );

        return $alumniRequest;
    }
}

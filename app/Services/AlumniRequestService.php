<?php

namespace App\Services;

use App\Models\Alumni;
use App\Models\AlumniRequest;
use App\Models\User;
use App\Repositories\AlumniRepository;
use App\Repositories\AlumniRequestRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AlumniRequestService
{
    /**
     * Whitelist field alumni yang boleh diubah via permohonan.
     * Field di luar daftar ini ditolak saat approve untuk mencegah
     * injection field sensitif (id, user_id, deleted_by, dll.).
     *
     * @var array<string, string>  field_name => cast_type
     */
    private const ALLOWED_ALUMNI_FIELDS = [
        'name'                   => 'string',
        'gender'                 => 'string',
        'birth_place'            => 'string',
        'birth_date'             => 'string',
        'address'                => 'string',
        'city'                   => 'string',
        'province'               => 'string',
        'postal_code'            => 'string',
        'phone'                  => 'string',
        'email'                  => 'string',
        'graduation_year'        => 'integer',
        'graduation_date'        => 'string',
        'ipk'                    => 'float',
        'thesis_title'           => 'string',
        'is_employed'            => 'boolean',
        'employment_status'      => 'string',
        'waiting_period_months'  => 'integer',
        'study_program_id'       => 'string',
    ];

    public function __construct(
        protected AlumniRequestRepository $requestRepository,
        protected AlumniRepository        $alumniRepository,
        protected AuditService            $auditService
    ) {}

    // ─── Read ────────────────────────────────────────────────────────────────────────

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

    // ─── Write ──────────────────────────────────────────────────────────────────────

    /**
     * Alumni atau admin mengajukan permohonan perubahan data.
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
     * Update permohonan yang masih menunggu (admin atau pemilik).
     */
    public function update(
        AlumniRequest $alumniRequest,
        array $validated,
        User $actor
    ): AlumniRequest {
        if (! $alumniRequest->isPending()) {
            throw ValidationException::withMessages([
                'status' => ['Hanya permohonan berstatus menunggu yang dapat diubah.'],
            ]);
        }

        $oldValues = $alumniRequest->toArray();

        $updated = $this->requestRepository->update($alumniRequest, array_merge(
            $validated,
            ['updated_by' => $actor->id]
        ));

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->toArray(),
            actor: $actor
        );

        return $updated;
    }

    /**
     * Admin menyetujui permohonan dan menerapkan perubahan ke data alumni.
     */
    public function approve(
        AlumniRequest $alumniRequest,
        User          $actor,
        ?string       $notes = null
    ): AlumniRequest {
        if (! $alumniRequest->isPending()) {
            throw ValidationException::withMessages([
                'status' => ['Hanya permohonan berstatus menunggu yang dapat disetujui.'],
            ]);
        }

        $oldValues = $alumniRequest->toArray();

        // Terapkan perubahan ke data alumni untuk tipe data (bukan tipe verifikasi)
        $applyableTypes = [
            AlumniRequest::TYPE_UPDATE_AKADEMIK,
            AlumniRequest::TYPE_UPDATE_PROFIL,
        ];

        if (in_array($alumniRequest->type, $applyableTypes, true)) {
            $alumni = $this->alumniRepository->findById($alumniRequest->alumni_id);
            if ($alumni) {
                $this->applyToAlumni($alumniRequest, $alumni, $actor);
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
        User          $actor,
        ?string       $notes = null
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

    // ─── Private Helpers ──────────────────────────────────────────────────────────

    /**
     * Terapkan new_value dari AlumniRequest ke record Alumni.
     *
     * Keamanan:
     *  1. Field di luar ALLOWED_ALUMNI_FIELDS ditolak (abort 422).
     *  2. Nilai di-cast sesuai tipe kolom sebelum disimpan
     *     (mencegah type confusion: '1' sebagai boolean, '3.5' sebagai float, dll.).
     *  3. Update direkam dengan updated_by = actor->id untuk audit trail.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException  jika field tidak diizinkan
     */
    protected function applyToAlumni(
        AlumniRequest $alumniRequest,
        Alumni        $alumni,
        User          $actor
    ): void {
        $field = $alumniRequest->field_name;

        // 1. Whitelist check
        if (! array_key_exists($field, self::ALLOWED_ALUMNI_FIELDS)) {
            abort(422, "Field '{$field}' tidak diizinkan untuk diubah melalui permohonan.");
        }

        // 2. Cast nilai sesuai tipe kolom
        $castType = self::ALLOWED_ALUMNI_FIELDS[$field];
        $castValue = match ($castType) {
            'integer' => (int)   $alumniRequest->new_value,
            'float'   => (float) $alumniRequest->new_value,
            'boolean' => filter_var($alumniRequest->new_value, FILTER_VALIDATE_BOOLEAN),
            default   =>          $alumniRequest->new_value,  // string / date tetap string
        };

        // 3. Simpan ke alumni
        $this->alumniRepository->update($alumni, [
            $field       => $castValue,
            'updated_by' => $actor->id,
        ]);
    }
}

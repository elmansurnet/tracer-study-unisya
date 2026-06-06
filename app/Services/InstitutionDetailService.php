<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\InstitutionDetail;
use App\Repositories\InstitutionDetailRepository;
use App\Repositories\InstitutionRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class InstitutionDetailService
{
    public function __construct(
        protected InstitutionDetailRepository $detailRepository,
        protected InstitutionRepository       $institutionRepository,
        protected AuditService                $auditService
    ) {}

    /**
     * Temukan detail berdasarkan institution_id.
     * Jika belum ada, kembalikan null (tidak auto-create).
     */
    public function findByInstitution(string $institutionId): ?InstitutionDetail
    {
        return $this->detailRepository->findByInstitutionId($institutionId);
    }

    /**
     * Simpan (buat atau perbarui) detail institusi.
     */
    public function upsert(
        Institution         $institution,
        array               $validated,
        Authenticatable     $actor
    ): InstitutionDetail {
        $existing = $this->detailRepository->findByInstitutionId($institution->id);
        $isNew    = $existing === null;

        $detail = $this->detailRepository->upsert($institution->id, array_merge($validated, [
            'updated_by' => $actor->id,
        ]));

        $this->auditService->log(
            action:      $isNew ? 'CREATE' : 'UPDATE',
            model:       InstitutionDetail::class,
            modelId:     $detail->id ?? $institution->id,
            description: ($isNew ? 'Membuat' : 'Memperbarui') . ' detail institusi: ' . $institution->name,
            actor:       $actor
        );

        return $detail->fresh();
    }
}

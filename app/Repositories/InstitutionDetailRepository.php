<?php

namespace App\Repositories;

use App\Models\InstitutionDetail;

class InstitutionDetailRepository
{
    /**
     * Ambil detail institusi berdasarkan institution_id.
     */
    public function findByInstitutionId(string $institutionId): ?InstitutionDetail
    {
        return InstitutionDetail::where('institution_id', $institutionId)->first();
    }

    /**
     * Buat atau perbarui detail institusi.
     */
    public function upsert(string $institutionId, array $data): InstitutionDetail
    {
        return InstitutionDetail::updateOrCreate(
            ['institution_id' => $institutionId],
            $data
        );
    }
}

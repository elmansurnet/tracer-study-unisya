<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateInstitutionDetailRequest;
use App\Http\Resources\InstitutionDetailResource;
use App\Http\Resources\InstitutionResource;
use App\Models\Institution;
use App\Models\InstitutionDetail;
use App\Services\InstitutionDetailService;
use App\Services\InstitutionService;
use Illuminate\Http\JsonResponse;

class InstitutionDetailController extends Controller
{
    public function __construct(
        protected InstitutionDetailService $detailService,
        protected InstitutionService       $institutionService
    ) {}

    /**
     * GET /api/v1/admin/institutions/{id}/detail
     * Ambil detail institusi.
     */
    public function show(string $id): JsonResponse
    {
        $institution = $this->institutionService->findOrFail($id);
        $this->authorize('view', $institution);

        $detail = $this->detailService->findByInstitution($institution->id);

        return response()->json([
            'data' => $detail ? new InstitutionDetailResource($detail) : null,
        ]);
    }

    /**
     * PUT /api/v1/admin/institutions/{id}/detail
     * Simpan atau perbarui detail institusi (upsert).
     */
    public function upsert(UpdateInstitutionDetailRequest $request, string $id): JsonResponse
    {
        $institution = $this->institutionService->findOrFail($id);
        $this->authorize('update', $institution);

        $detail = $this->detailService->upsert(
            institution: $institution,
            validated:   $request->validated(),
            actor:       $request->user()
        );

        return response()->json([
            'message' => 'Detail institusi berhasil disimpan.',
            'data'    => new InstitutionDetailResource($detail),
        ]);
    }
}

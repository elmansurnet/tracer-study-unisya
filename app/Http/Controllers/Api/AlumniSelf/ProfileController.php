<?php

namespace App\Http\Controllers\Api\AlumniSelf;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlumniSelf\UpdateEmploymentRequest;
use App\Http\Requests\AlumniSelf\UpdateProfileRequest;
use App\Http\Resources\AlumniResource;
use App\Services\AlumniService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(protected AlumniService $alumniService) {}

    /**
     * GET /api/v1/alumni/profile
     * Menampilkan profil alumni yang sedang login.
     */
    public function show(Request $request): JsonResponse
    {
        $user   = $request->user();
        $alumni = $user->alumni;

        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $this->authorize('viewSelf', $alumni);

        return response()->json([
            'data' => new AlumniResource($alumni->load([
                'studyProgram.faculty',
                'employmentHistories.institution',
                'employmentHistories.profession.category',
            ])),
        ]);
    }

    /**
     * PATCH /api/v1/alumni/profile
     * Alumni memperbarui data kontak/profil diri sendiri.
     * Field akademik (NIM, graduation_year, dll) TIDAK dapat diubah.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user   = $request->user();
        $alumni = $user->alumni;

        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $this->authorize('updateSelf', $alumni);

        $updated = $this->alumniService->update(
            alumni:    $alumni,
            validated: $request->validated(),
            actor:     $user
        );

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data'    => new AlumniResource($updated),
        ]);
    }

    /**
     * PATCH /api/v1/alumni/employment-status
     * Alumni memperbarui status pekerjaan diri sendiri secara atomik.
     */
    public function updateEmploymentStatus(UpdateEmploymentRequest $request): JsonResponse
    {
        $user   = $request->user();
        $alumni = $user->alumni;

        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $this->authorize('updateSelf', $alumni);

        $validated = $request->validated();

        $updated = $this->alumniService->updateEmploymentStatus(
            alumni:               $alumni,
            status:               $validated['employment_status'],
            isEmployed:           (bool) $validated['is_employed'],
            waitingPeriodMonths:  $validated['waiting_period_months'] ?? null,
            actor:                $user
        );

        return response()->json([
            'message' => 'Status pekerjaan berhasil diperbarui.',
            'data'    => new AlumniResource($updated),
        ]);
    }
}

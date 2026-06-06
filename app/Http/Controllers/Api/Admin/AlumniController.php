<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAlumniRequest;
use App\Http\Requests\Admin\UpdateAlumniRequest;
use App\Http\Resources\AlumniResource;
use App\Models\Alumni;
use App\Services\AlumniService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AlumniController extends Controller
{
    public function __construct(protected AlumniService $alumniService) {}

    /**
     * GET /api/v1/admin/alumni
     * Daftar alumni dengan paginasi & filter.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Alumni::class);

        $alumni = $this->alumniService->paginate(
            perPage: (int) $request->input('per_page', 15),
            filters: $request->only([
                'search',
                'study_program_id',
                'faculty_id',
                'graduation_year',
                'employment_status',
                'is_employed',
                'gender',
                'with_trashed',
            ]),
            sortBy:  $request->input('sort_by', 'name'),
            sortDir: $request->input('sort_dir', 'asc')
        );

        return AlumniResource::collection($alumni);
    }

    /**
     * GET /api/v1/admin/alumni/graduation-years
     * Daftar tahun wisuda yang tersedia (untuk dropdown filter).
     */
    public function graduationYears(): JsonResponse
    {
        $this->authorize('viewAny', Alumni::class);

        return response()->json([
            'data' => $this->alumniService->graduationYears(),
        ]);
    }

    /**
     * GET /api/v1/admin/alumni/employment-stats
     * Statistik status pekerjaan alumni (opsional filter by graduation_year).
     */
    public function employmentStats(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Alumni::class);

        $year = $request->query('graduation_year') ? (int) $request->query('graduation_year') : null;

        return response()->json([
            'data' => $this->alumniService->countByEmploymentStatus($year),
        ]);
    }

    /**
     * GET /api/v1/admin/study-programs/{studyProgramId}/alumni
     * Daftar alumni per prodi (tanpa paginasi, untuk dropdown).
     */
    public function byStudyProgram(Request $request, string $studyProgramId): JsonResponse
    {
        $this->authorize('viewAny', Alumni::class);

        $year    = $request->query('graduation_year') ? (int) $request->query('graduation_year') : null;
        $alumni  = $this->alumniService->byStudyProgram($studyProgramId, $year);

        return response()->json([
            'data' => AlumniResource::collection($alumni),
        ]);
    }

    /**
     * POST /api/v1/admin/alumni
     */
    public function store(StoreAlumniRequest $request): JsonResponse
    {
        $this->authorize('create', Alumni::class);

        $alumni = $this->alumniService->create(
            validated: $request->validated(),
            actor:     $request->user()
        );

        return response()->json([
            'message' => 'Data alumni berhasil ditambahkan.',
            'data'    => new AlumniResource($alumni),
        ], 201);
    }

    /**
     * GET /api/v1/admin/alumni/{id}
     */
    public function show(string $id): JsonResponse
    {
        $alumni = $this->alumniService->findOrFail($id);
        $this->authorize('view', $alumni);

        return response()->json([
            'data' => new AlumniResource($alumni->load([
                'studyProgram.faculty',
                'user',
                'employmentHistories.institution',
                'employmentHistories.profession.category',
            ])),
        ]);
    }

    /**
     * PUT /api/v1/admin/alumni/{id}
     */
    public function update(UpdateAlumniRequest $request, string $id): JsonResponse
    {
        $alumni = $this->alumniService->findOrFail($id);
        $this->authorize('update', $alumni);

        $updated = $this->alumniService->update(
            alumni:    $alumni,
            validated: $request->validated(),
            actor:     $request->user()
        );

        return response()->json([
            'message' => 'Data alumni berhasil diperbarui.',
            'data'    => new AlumniResource($updated),
        ]);
    }

    /**
     * DELETE /api/v1/admin/alumni/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $alumni = $this->alumniService->findOrFail($id);
        $this->authorize('delete', $alumni);

        $this->alumniService->delete($alumni, $request->user());

        return response()->json([
            'message' => 'Data alumni berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/admin/alumni/{id}/restore
     */
    public function restore(Request $request, string $id): JsonResponse
    {
        $this->authorize('restore', Alumni::class);

        $alumni = $this->alumniService->restore($id, $request->user());

        return response()->json([
            'message' => 'Data alumni berhasil dipulihkan.',
            'data'    => new AlumniResource($alumni),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudyProgramRequest;
use App\Http\Requests\Admin\UpdateStudyProgramRequest;
use App\Http\Resources\StudyProgramResource;
use App\Models\StudyProgram;
use App\Services\StudyProgramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudyProgramController extends Controller
{
    public function __construct(protected StudyProgramService $studyProgramService) {}

    /**
     * GET /api/v1/admin/study-programs
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', StudyProgram::class);

        $programs = $this->studyProgramService->paginate(
            perPage: (int) $request->input('per_page', 15),
            filters: $request->only(['search', 'faculty_id', 'degree_level', 'is_active']),
            sortBy:  $request->input('sort_by', 'name'),
            sortDir: $request->input('sort_dir', 'asc')
        );

        return StudyProgramResource::collection($programs);
    }

    /**
     * GET /api/v1/admin/study-programs/all
     * Dropdown — semua prodi aktif tanpa paginasi.
     */
    public function all(Request $request): JsonResponse
    {
        $this->authorize('viewAny', StudyProgram::class);

        $collection = $request->has('faculty_id')
            ? $this->studyProgramService->byFaculty($request->input('faculty_id'))
            : $this->studyProgramService->allActive();

        return response()->json([
            'data' => StudyProgramResource::collection($collection),
        ]);
    }

    /**
     * POST /api/v1/admin/study-programs
     */
    public function store(StoreStudyProgramRequest $request): JsonResponse
    {
        $this->authorize('create', StudyProgram::class);

        $sp = $this->studyProgramService->create(
            validated: $request->validated(),
            actor: $request->user()
        );

        return response()->json([
            'message' => 'Program studi berhasil ditambahkan.',
            'data'    => new StudyProgramResource($sp),
        ], 201);
    }

    /**
     * GET /api/v1/admin/study-programs/{id}
     */
    public function show(string $id): JsonResponse
    {
        $sp = $this->studyProgramService->findOrFail($id);
        $this->authorize('view', $sp);

        return response()->json([
            'data' => new StudyProgramResource($sp),
        ]);
    }

    /**
     * PUT /api/v1/admin/study-programs/{id}
     */
    public function update(UpdateStudyProgramRequest $request, string $id): JsonResponse
    {
        $sp = $this->studyProgramService->findOrFail($id);
        $this->authorize('update', $sp);

        $updated = $this->studyProgramService->update(
            sp:        $sp,
            validated: $request->validated(),
            actor:     $request->user()
        );

        return response()->json([
            'message' => 'Data program studi berhasil diperbarui.',
            'data'    => new StudyProgramResource($updated),
        ]);
    }

    /**
     * DELETE /api/v1/admin/study-programs/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $sp = $this->studyProgramService->findOrFail($id);
        $this->authorize('delete', $sp);

        $this->studyProgramService->delete($sp, $request->user());

        return response()->json([
            'message' => 'Program studi berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/admin/study-programs/{id}/restore
     */
    public function restore(Request $request, string $id): JsonResponse
    {
        $this->authorize('restore', StudyProgram::class);

        $sp = $this->studyProgramService->restore($id, $request->user());

        return response()->json([
            'message' => 'Program studi berhasil dipulihkan.',
            'data'    => new StudyProgramResource($sp),
        ]);
    }
}

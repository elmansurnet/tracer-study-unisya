<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnswerType\StoreAnswerTypeRequest;
use App\Http\Requests\Admin\AnswerType\UpdateAnswerTypeRequest;
use App\Http\Resources\Admin\AnswerTypeCollection;
use App\Http\Resources\Admin\AnswerTypeResource;
use App\Services\AnswerTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnswerTypeController extends Controller
{
    public function __construct(
        protected AnswerTypeService $service
    ) {}

    /** GET /admin/answer-types */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\AnswerType::class);

        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['search', 'is_active']);

        $paginated = $this->service->paginate($filters, $perPage);

        return response()->json(new AnswerTypeCollection($paginated));
    }

    /** GET /admin/answer-types/all */
    public function all(): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\AnswerType::class);

        $types = $this->service->all(onlyActive: true);

        return response()->json([
            'data' => AnswerTypeResource::collection($types),
        ]);
    }

    /** POST /admin/answer-types */
    public function store(StoreAnswerTypeRequest $request): JsonResponse
    {
        $answerType = $this->service->create($request->validated());

        return response()->json(new AnswerTypeResource($answerType), 201);
    }

    /** GET /admin/answer-types/{answer_type} */
    public function show(string $id): JsonResponse
    {
        $answerType = $this->service->findByIdOrFail($id);
        $this->authorize('view', $answerType);

        return response()->json(new AnswerTypeResource($answerType));
    }

    /** PUT /admin/answer-types/{answer_type} */
    public function update(UpdateAnswerTypeRequest $request, string $id): JsonResponse
    {
        $answerType = $this->service->findByIdOrFail($id);
        $this->authorize('update', $answerType);

        $updated = $this->service->update($answerType, $request->validated());

        return response()->json(new AnswerTypeResource($updated));
    }

    /** DELETE /admin/answer-types/{answer_type} */
    public function destroy(string $id): JsonResponse
    {
        $answerType = $this->service->findByIdOrFail($id);
        $this->authorize('delete', $answerType);

        $this->service->delete($answerType);

        return response()->json(['message' => 'Tipe jawaban berhasil dihapus.']);
    }
}

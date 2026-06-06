<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditTrailController extends Controller
{
    /**
     * GET /api/v1/admin/audit-trails
     * Daftar audit trail dengan filter dan paginasi.
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $this->authorize('viewAny', AuditTrail::class);

        $query = AuditTrail::with(['user:id,name,email'])
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('action'),  fn ($q) => $q->where('action', strtoupper($request->action)))
            ->when($request->filled('model'),   fn ($q) => $q->where('auditable_type', 'LIKE', '%' . $request->model . '%'))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('description', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('ip_address', 'LIKE', '%' . $request->search . '%');
                });
            })
            ->orderByDesc('created_at');

        $perPage = (int) $request->input('per_page', 20);
        $results = $query->paginate($perPage);

        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'current_page' => $results->currentPage(),
                'last_page'    => $results->lastPage(),
                'per_page'     => $results->perPage(),
                'total'        => $results->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/admin/audit-trails/{id}
     * Detail satu entry audit trail.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('viewAny', AuditTrail::class);

        $audit = AuditTrail::with(['user:id,name,email'])->findOrFail($id);

        return response()->json(['data' => $audit]);
    }
}

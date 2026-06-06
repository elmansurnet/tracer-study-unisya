<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * GET /api/v1/admin/activity-logs
     * Daftar activity log dengan filter dan paginasi.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Activity::class);

        $query = Activity::with(['causer', 'subject'])
            ->when($request->filled('log_name'),    fn ($q) => $q->inLog($request->log_name))
            ->when($request->filled('causer_type'), fn ($q) => $q->causedBy($request->causer_type))
            ->when($request->filled('causer_id'),   fn ($q) => $q->where('causer_id', $request->causer_id))
            ->when($request->filled('event'),       fn ($q) => $q->where('event', $request->event))
            ->when($request->filled('date_from'),   fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),     fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('description', 'LIKE', '%' . $request->search . '%');
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
     * GET /api/v1/admin/activity-logs/{id}
     * Detail satu activity log.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('viewAny', Activity::class);

        $activity = Activity::with(['causer', 'subject'])->findOrFail($id);

        return response()->json(['data' => $activity]);
    }

    /**
     * DELETE /api/v1/admin/activity-logs
     * Bersihkan activity log (admin only).
     */
    public function purge(Request $request): JsonResponse
    {
        $this->authorize('purge', Activity::class);

        $before = $request->input('before'); // tanggal opsional: hapus log sebelum tanggal ini

        $count = Activity::when($before, fn ($q) => $q->whereDate('created_at', '<', $before))
            ->delete();

        return response()->json([
            'message' => "Berhasil menghapus {$count} activity log.",
        ]);
    }
}

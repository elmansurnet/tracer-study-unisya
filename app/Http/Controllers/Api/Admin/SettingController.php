<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService) {}

    /**
     * GET /api/v1/admin/settings
     * Ambil semua setting (dikelompokkan per group).
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', AppSetting::class);

        $groups = AppSetting::query()
            ->when($request->filled('group'), fn ($q) => $q->where('group', $request->group))
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->map(fn ($item) => [
                'key'          => $item->key,
                'label'        => $item->label,
                'type'         => $item->type,
                'value'        => $item->is_encrypted ? '[ENCRYPTED]' : $item->value,
                'is_encrypted' => $item->is_encrypted,
                'is_public'    => $item->is_public,
                'updated_at'   => $item->updated_at?->toIso8601String(),
            ]));

        return response()->json(['data' => $groups]);
    }

    /**
     * GET /api/v1/admin/settings/{group}/{key}
     * Ambil satu setting.
     */
    public function show(string $group, string $key): JsonResponse
    {
        $this->authorize('viewAny', AppSetting::class);

        $setting = AppSetting::where('group', $group)
            ->where('key', $key)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'group'        => $setting->group,
                'key'          => $setting->key,
                'label'        => $setting->label,
                'type'         => $setting->type,
                'value'        => $setting->is_encrypted ? '[ENCRYPTED]' : $setting->value,
                'is_encrypted' => $setting->is_encrypted,
                'is_public'    => $setting->is_public,
            ],
        ]);
    }

    /**
     * PUT /api/v1/admin/settings/{group}/{key}
     * Perbarui nilai setting.
     */
    public function update(Request $request, string $group, string $key): JsonResponse
    {
        $this->authorize('update', AppSetting::class);

        $validator = Validator::make($request->all(), [
            'value' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting = AppSetting::where('group', $group)
            ->where('key', $key)
            ->firstOrFail();

        $this->settingService->set(
            group:   $group,
            key:     $key,
            value:   $request->value,
            encrypt: $setting->is_encrypted
        );

        return response()->json([
            'message' => 'Pengaturan berhasil disimpan.',
        ]);
    }

    /**
     * PUT /api/v1/admin/settings/batch
     * Perbarui banyak setting sekaligus.
     */
    public function batchUpdate(Request $request): JsonResponse
    {
        $this->authorize('update', AppSetting::class);

        $validator = Validator::make($request->all(), [
            'settings'           => ['required', 'array'],
            'settings.*.group'   => ['required', 'string'],
            'settings.*.key'     => ['required', 'string'],
            'settings.*.value'   => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->settings as $item) {
            $setting = AppSetting::where('group', $item['group'])
                ->where('key', $item['key'])
                ->first();

            if ($setting) {
                $this->settingService->set(
                    group:   $item['group'],
                    key:     $item['key'],
                    value:   $item['value'],
                    encrypt: $setting->is_encrypted
                );
            }
        }

        return response()->json([
            'message' => 'Semua pengaturan berhasil disimpan.',
        ]);
    }
}

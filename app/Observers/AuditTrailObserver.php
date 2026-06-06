<?php

namespace App\Observers;

use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditTrailObserver
{
    /**
     * Model yang dikecualikan dari pencatatan otomatis.
     * (AuditTrail itu sendiri tidak boleh di-observe untuk menghindari infinite loop)
     */
    private array $excluded = [
        AuditTrail::class,
    ];

    public function created(Model $model): void
    {
        $this->record('CREATE', $model);
    }

    public function updated(Model $model): void
    {
        $this->record('UPDATE', $model);
    }

    public function deleted(Model $model): void
    {
        $this->record('DELETE', $model);
    }

    public function restored(Model $model): void
    {
        $this->record('RESTORE', $model);
    }

    /**
     * Rekam perubahan ke tabel audit_trails.
     */
    private function record(string $action, Model $model): void
    {
        if (in_array(get_class($model), $this->excluded, true)) {
            return;
        }

        // Ambil hanya field yang berubah (untuk UPDATE)
        $newValues = match ($action) {
            'UPDATE'  => $model->getDirty(),
            'CREATE'  => $model->getAttributes(),
            'DELETE',
            'RESTORE' => ['id' => $model->getKey()],
            default   => [],
        };

        // Nilai lama (sebelum UPDATE)
        $oldValues = $action === 'UPDATE'
            ? collect($model->getDirty())->keys()
                ->mapWithKeys(fn ($key) => [$key => $model->getOriginal($key)])
                ->toArray()
            : [];

        // Hapus field sensitif
        $sensitiveFields = ['password', 'remember_token', 'api_token'];
        foreach ($sensitiveFields as $field) {
            unset($newValues[$field], $oldValues[$field]);
        }

        AuditTrail::create([
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => get_class($model),
            'auditable_id'   => (string) $model->getKey(),
            'old_values'     => empty($oldValues) ? null : $oldValues,
            'new_values'     => empty($newValues) ? null : $newValues,
            'description'    => $action . ' ' . class_basename($model) . ' #' . $model->getKey(),
            'ip_address'     => Request::ip(),
            'user_agent'     => Request::userAgent(),
        ]);
    }
}

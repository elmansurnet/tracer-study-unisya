<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AppSetting extends Model
{
    use HasUuids;

    protected $fillable = ['group', 'key', 'value', 'type', 'label', 'description', 'is_encrypted', 'updated_by'];

    protected function casts(): array
    {
        return ['is_encrypted' => 'boolean'];
    }

    public function getDecryptedValue(): mixed
    {
        if (! $this->value) {
            return null;
        }

        if ($this->is_encrypted) {
            return Crypt::decryptString($this->value);
        }

        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'integer' => (int) $this->value,
            'json'    => json_decode($this->value, true),
            default   => $this->value,
        };
    }
}
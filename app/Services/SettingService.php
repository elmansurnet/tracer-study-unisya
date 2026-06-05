<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SettingService
{
    private const CACHE_TTL    = 3600; // 1 jam
    private const CACHE_PREFIX = 'app_setting_';

    /**
     * Ambil nilai setting berdasarkan group dan key.
     */
    public function get(string $group, string $key, mixed $default = null): mixed
    {
        $cacheKey = self::CACHE_PREFIX . $group . '_' . $key;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($group, $key, $default) {
            $setting = AppSetting::where('group', $group)
                ->where('key', $key)
                ->first();

            if (! $setting) {
                return $default;
            }

            return $setting->getDecryptedValue();
        });
    }

    /**
     * Set nilai setting.
     */
    public function set(string $group, string $key, mixed $value, bool $encrypt = false): void
    {
        $storedValue = $encrypt
            ? Crypt::encryptString((string) $value)
            : (is_array($value) ? json_encode($value) : (string) $value);

        AppSetting::updateOrCreate(
            ['group' => $group, 'key' => $key],
            [
                'value'        => $storedValue,
                'is_encrypted' => $encrypt,
                'updated_by'   => auth()->id(),
            ]
        );

        // Hapus cache
        Cache::forget(self::CACHE_PREFIX . $group . '_' . $key);
    }

    // Shorthand helpers
    public function getWaGatewayApiKey(): ?string
    {
        return $this->get('wa_gateway', 'api_key');
    }

    public function getWaGatewaySender(): ?string
    {
        return $this->get('wa_gateway', 'sender');
    }

    public function getSmtpConfig(): array
    {
        return [
            'host'       => $this->get('smtp', 'host', ''),
            'port'       => $this->get('smtp', 'port', 587),
            'encryption' => $this->get('smtp', 'encryption', 'tls'),
            'username'   => $this->get('smtp', 'username', ''),
            'password'   => $this->get('smtp', 'password', ''),
        ];
    }
}
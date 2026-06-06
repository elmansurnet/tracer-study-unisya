<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ─── Informasi Universitas ──────────────────────────────────
            [
                'group'        => 'university',
                'key'          => 'name',
                'label'        => 'Nama Universitas',
                'type'         => 'text',
                'value'        => 'Universitas Islam Syarifuddin Lumajang',
                'is_encrypted' => false,
                'is_public'    => true,
            ],
            [
                'group'        => 'university',
                'key'          => 'short_name',
                'label'        => 'Singkatan Universitas',
                'type'         => 'text',
                'value'        => 'UNISYA',
                'is_encrypted' => false,
                'is_public'    => true,
            ],
            [
                'group'        => 'university',
                'key'          => 'address',
                'label'        => 'Alamat Universitas',
                'type'         => 'text',
                'value'        => 'Jl. KHW Hasyim No. 54 Lumajang, Jawa Timur',
                'is_encrypted' => false,
                'is_public'    => true,
            ],
            [
                'group'        => 'university',
                'key'          => 'email',
                'label'        => 'Email Universitas',
                'type'         => 'text',
                'value'        => 'info@unisya.ac.id',
                'is_encrypted' => false,
                'is_public'    => true,
            ],
            [
                'group'        => 'university',
                'key'          => 'phone',
                'label'        => 'Telepon Universitas',
                'type'         => 'text',
                'value'        => '(0334) 881027',
                'is_encrypted' => false,
                'is_public'    => true,
            ],
            [
                'group'        => 'university',
                'key'          => 'website',
                'label'        => 'Website Universitas',
                'type'         => 'text',
                'value'        => 'https://unisya.ac.id',
                'is_encrypted' => false,
                'is_public'    => true,
            ],
            [
                'group'        => 'university',
                'key'          => 'logo_url',
                'label'        => 'URL Logo Universitas',
                'type'         => 'text',
                'value'        => '',
                'is_encrypted' => false,
                'is_public'    => true,
            ],

            // ─── Pengaturan Tracer Study ────────────────────────────────
            [
                'group'        => 'tracer',
                'key'          => 'survey_open',
                'label'        => 'Status Survei Terbuka',
                'type'         => 'boolean',
                'value'        => 'true',
                'is_encrypted' => false,
                'is_public'    => false,
            ],
            [
                'group'        => 'tracer',
                'key'          => 'waiting_period_years',
                'label'        => 'Periode Tunggu Kerja (Tahun)',
                'type'         => 'number',
                'value'        => '2',
                'is_encrypted' => false,
                'is_public'    => false,
            ],
            [
                'group'        => 'tracer',
                'key'          => 'graduation_year_range',
                'label'        => 'Rentang Tahun Lulus yang Disurvei',
                'type'         => 'number',
                'value'        => '5',
                'is_encrypted' => false,
                'is_public'    => false,
            ],

            // ─── WhatsApp Gateway ───────────────────────────────────────
            [
                'group'        => 'wa_gateway',
                'key'          => 'provider',
                'label'        => 'Provider WA Gateway',
                'type'         => 'text',
                'value'        => 'fonnte',
                'is_encrypted' => false,
                'is_public'    => false,
            ],
            [
                'group'        => 'wa_gateway',
                'key'          => 'api_key',
                'label'        => 'API Key WA Gateway',
                'type'         => 'text',
                'value'        => '',
                'is_encrypted' => true,
                'is_public'    => false,
            ],
            [
                'group'        => 'wa_gateway',
                'key'          => 'sender',
                'label'        => 'Nomor Pengirim WA',
                'type'         => 'text',
                'value'        => '',
                'is_encrypted' => false,
                'is_public'    => false,
            ],
            [
                'group'        => 'wa_gateway',
                'key'          => 'active',
                'label'        => 'WA Gateway Aktif',
                'type'         => 'boolean',
                'value'        => 'false',
                'is_encrypted' => false,
                'is_public'    => false,
            ],

            // ─── Notifikasi ─────────────────────────────────────────────
            [
                'group'        => 'notification',
                'key'          => 'send_otp_via_wa',
                'label'        => 'Kirim OTP via WhatsApp',
                'type'         => 'boolean',
                'value'        => 'false',
                'is_encrypted' => false,
                'is_public'    => false,
            ],
            [
                'group'        => 'notification',
                'key'          => 'reminder_enabled',
                'label'        => 'Aktifkan Reminder Pengisian Survei',
                'type'         => 'boolean',
                'value'        => 'false',
                'is_encrypted' => false,
                'is_public'    => false,
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                [
                    'group' => $setting['group'],
                    'key'   => $setting['key'],
                ],
                $setting
            );
        }
    }
}

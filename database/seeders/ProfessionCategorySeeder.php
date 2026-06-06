<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfessionCategorySeeder extends Seeder
{
    /**
     * Kategori profesi umum untuk alumni tracer study.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Pendidikan dan Pelatihan',
                'description' => 'Profesi di bidang pendidikan formal, non-formal, dan pelatihan SDM.',
            ],
            [
                'name'        => 'Hukum dan Kepatuhan',
                'description' => 'Profesi di bidang hukum, advokasi, kepatuhan regulasi, dan notariat.',
            ],
            [
                'name'        => 'Ekonomi dan Keuangan',
                'description' => 'Profesi di bidang ekonomi, akuntansi, perbankan, dan keuangan.',
            ],
            [
                'name'        => 'Teknologi dan Informatika',
                'description' => 'Profesi di bidang teknologi informasi, rekayasa perangkat lunak, dan sistem informasi.',
            ],
            [
                'name'        => 'Teknik dan Infrastruktur',
                'description' => 'Profesi di bidang teknik sipil, arsitektur, dan infrastruktur.',
            ],
            [
                'name'        => 'Kesehatan dan Medis',
                'description' => 'Profesi di bidang kesehatan, kedokteran, keperawatan, dan farmasi.',
            ],
            [
                'name'        => 'Agama dan Dakwah',
                'description' => 'Profesi di bidang keagamaan, dakwah, dan penyuluhan agama.',
            ],
            [
                'name'        => 'Pemerintahan dan Administrasi Publik',
                'description' => 'Profesi di sektor pemerintahan, birokrasi, dan pelayanan publik.',
            ],
            [
                'name'        => 'Bisnis dan Wirausaha',
                'description' => 'Profesi di bidang wirausaha, manajemen bisnis, dan perdagangan.',
            ],
            [
                'name'        => 'Sosial dan Kemasyarakatan',
                'description' => 'Profesi di bidang pekerjaan sosial, pemberdayaan masyarakat, dan LSM.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('profession_categories')->insertOrIgnore([
                'id'          => Str::ulid(),
                'name'        => $category['name'],
                'description' => $category['description'],
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}

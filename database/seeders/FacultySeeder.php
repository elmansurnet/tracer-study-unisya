<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacultySeeder extends Seeder
{
    /**
     * Data Fakultas UNISYA (Universitas Islam Syarifuddin)
     * Sumber: Data institusi resmi
     */
    public function run(): void
    {
        $faculties = [
            [
                'code'        => 'FAI',
                'name'        => 'Fakultas Agama Islam',
                'description' => 'Fakultas yang menyelenggarakan pendidikan tinggi di bidang ilmu agama Islam.',
            ],
            [
                'code'        => 'FE',
                'name'        => 'Fakultas Ekonomi',
                'description' => 'Fakultas yang menyelenggarakan pendidikan tinggi di bidang ilmu ekonomi dan bisnis.',
            ],
            [
                'code'        => 'FH',
                'name'        => 'Fakultas Hukum',
                'description' => 'Fakultas yang menyelenggarakan pendidikan tinggi di bidang ilmu hukum.',
            ],
            [
                'code'        => 'FKIP',
                'name'        => 'Fakultas Keguruan dan Ilmu Pendidikan',
                'description' => 'Fakultas yang menyelenggarakan pendidikan tinggi di bidang keguruan dan pendidikan.',
            ],
            [
                'code'        => 'FT',
                'name'        => 'Fakultas Teknik',
                'description' => 'Fakultas yang menyelenggarakan pendidikan tinggi di bidang ilmu teknik dan teknologi.',
            ],
        ];

        foreach ($faculties as $faculty) {
            DB::table('faculties')->insertOrIgnore([
                'id'          => Str::ulid(),
                'code'        => $faculty['code'],
                'name'        => $faculty['name'],
                'description' => $faculty['description'],
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}

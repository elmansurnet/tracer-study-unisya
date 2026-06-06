<?php

namespace Database\Seeders;

use App\Models\ProfessionCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProfessionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Teknologi Informasi', 'description' => 'Bidang IT, software, hardware, dan sistem informasi'],
            ['name' => 'Pendidikan',           'description' => 'Tenaga pengajar, dosen, tutor, dan instruktur'],
            ['name' => 'Kesehatan',            'description' => 'Dokter, perawat, apoteker, dan tenaga medis lainnya'],
            ['name' => 'Keuangan & Akuntansi', 'description' => 'Akuntan, auditor, analis keuangan, dan banker'],
            ['name' => 'Hukum',                'description' => 'Pengacara, notaris, hakim, dan konsultan hukum'],
            ['name' => 'Teknik',               'description' => 'Insinyur sipil, mesin, elektro, dan industri'],
            ['name' => 'Bisnis & Manajemen',   'description' => 'Manajer, entrepreneur, konsultan bisnis'],
            ['name' => 'Pertanian & Peternakan','description' => 'Agronomi, peternakan, kehutanan, dan perikanan'],
            ['name' => 'Seni & Desain',         'description' => 'Desainer grafis, animator, fotografer, seniman'],
            ['name' => 'Komunikasi & Media',    'description' => 'Jurnalis, penyiar, PR, dan content creator'],
            ['name' => 'Lainnya',               'description' => 'Bidang pekerjaan lain yang tidak terklasifikasi'],
        ];

        foreach ($categories as $data) {
            ProfessionCategory::firstOrCreate(
                ['name' => $data['name']],
                [
                    'id'          => Str::ulid(),
                    'description' => $data['description'],
                    'is_active'   => 1,
                ]
            );
        }
    }
}

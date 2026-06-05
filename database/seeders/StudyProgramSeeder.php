<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudyProgramSeeder extends Seeder
{
    /**
     * Data Program Studi UNISYA (Universitas Islam Syarifuddin)
     * Sumber: Data institusi resmi
     */
    public function run(): void
    {
        // Ambil ID fakultas dari DB
        $faculties = DB::table('faculties')
            ->pluck('id', 'code')
            ->toArray();

        if (empty($faculties)) {
            $this->command->warn('Tabel faculties kosong. Jalankan FacultySeeder terlebih dahulu.');
            return;
        }

        $programs = [
            // Fakultas Agama Islam
            [
                'faculty_code' => 'FAI',
                'code'         => 'PAI',
                'name'         => 'Pendidikan Agama Islam',
                'degree_level' => 'S1',
            ],
            [
                'faculty_code' => 'FAI',
                'code'         => 'HES',
                'name'         => 'Hukum Ekonomi Syariah',
                'degree_level' => 'S1',
            ],
            [
                'faculty_code' => 'FAI',
                'code'         => 'KPI',
                'name'         => 'Komunikasi dan Penyiaran Islam',
                'degree_level' => 'S1',
            ],
            [
                'faculty_code' => 'FAI',
                'code'         => 'MHU',
                'name'         => 'Manajemen Haji dan Umroh',
                'degree_level' => 'S1',
            ],
            // Fakultas Ekonomi
            [
                'faculty_code' => 'FE',
                'code'         => 'MANAJ',
                'name'         => 'Manajemen',
                'degree_level' => 'S1',
            ],
            [
                'faculty_code' => 'FE',
                'code'         => 'AKUNT',
                'name'         => 'Akuntansi',
                'degree_level' => 'S1',
            ],
            [
                'faculty_code' => 'FE',
                'code'         => 'EKOSP',
                'name'         => 'Ekonomi Syariah',
                'degree_level' => 'S1',
            ],
            // Fakultas Hukum
            [
                'faculty_code' => 'FH',
                'code'         => 'ILHUK',
                'name'         => 'Ilmu Hukum',
                'degree_level' => 'S1',
            ],
            // Fakultas Keguruan dan Ilmu Pendidikan
            [
                'faculty_code' => 'FKIP',
                'code'         => 'PBSI',
                'name'         => 'Pendidikan Bahasa dan Sastra Indonesia',
                'degree_level' => 'S1',
            ],
            [
                'faculty_code' => 'FKIP',
                'code'         => 'PBING',
                'name'         => 'Pendidikan Bahasa Inggris',
                'degree_level' => 'S1',
            ],
            [
                'faculty_code' => 'FKIP',
                'code'         => 'PMAT',
                'name'         => 'Pendidikan Matematika',
                'degree_level' => 'S1',
            ],
            [
                'faculty_code' => 'FKIP',
                'code'         => 'PAUD',
                'name'         => 'Pendidikan Anak Usia Dini',
                'degree_level' => 'S1',
            ],
            // Fakultas Teknik
            [
                'faculty_code' => 'FT',
                'code'         => 'TEKNIK-INF',
                'name'         => 'Teknik Informatika',
                'degree_level' => 'S1',
            ],
            [
                'faculty_code' => 'FT',
                'code'         => 'TEKNIK-SIP',
                'name'         => 'Teknik Sipil',
                'degree_level' => 'S1',
            ],
        ];

        foreach ($programs as $program) {
            $facultyId = $faculties[$program['faculty_code']] ?? null;
            if (! $facultyId) {
                $this->command->warn("Fakultas '{$program['faculty_code']}' tidak ditemukan, skip '{$program['name']}'.");
                continue;
            }

            DB::table('study_programs')->insertOrIgnore([
                'id'           => Str::ulid(),
                'faculty_id'   => $facultyId,
                'code'         => $program['code'],
                'name'         => $program['name'],
                'degree_level' => $program['degree_level'],
                'description'  => null,
                'is_active'    => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}

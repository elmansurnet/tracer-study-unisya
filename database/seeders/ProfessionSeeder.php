<?php

namespace Database\Seeders;

use App\Models\Profession;
use App\Models\ProfessionCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Teknologi Informasi' => [
                'Software Engineer', 'Frontend Developer', 'Backend Developer',
                'Full Stack Developer', 'Mobile Developer', 'DevOps Engineer',
                'Data Scientist', 'Data Analyst', 'UI/UX Designer',
                'System Analyst', 'Database Administrator', 'Network Engineer',
                'IT Support', 'Cybersecurity Analyst', 'Project Manager IT',
            ],
            'Pendidikan' => [
                'Dosen', 'Guru SD/SMP/SMA', 'Instruktur Pelatihan',
                'Tutor Privat', 'Konselor Pendidikan',
            ],
            'Kesehatan' => [
                'Dokter Umum', 'Dokter Spesialis', 'Perawat',
                'Apoteker', 'Bidan', 'Ahli Gizi', 'Fisioterapis',
                'Radiografer', 'Analis Laboratorium',
            ],
            'Keuangan & Akuntansi' => [
                'Akuntan', 'Auditor', 'Analis Keuangan',
                'Staff Perpajakan', 'Treasury Staff', 'Financial Controller',
            ],
            'Teknik' => [
                'Insinyur Sipil', 'Insinyur Mesin', 'Insinyur Elektro',
                'Insinyur Industri', 'Insinyur Kimia', 'Insinyur Pertambangan',
                'Quality Control Engineer', 'Quantity Surveyor',
            ],
            'Bisnis & Manajemen' => [
                'Marketing Manager', 'Business Analyst', 'HR Manager',
                'Operations Manager', 'General Manager', 'Entrepreneur',
                'Sales Executive', 'Supply Chain Manager',
            ],
            'Komunikasi & Media' => [
                'Jurnalis', 'Editor', 'Content Creator',
                'Public Relations', 'Broadcaster', 'Fotografer',
                'Videografer', 'Social Media Specialist',
            ],
            'Seni & Desain' => [
                'Desainer Grafis', 'Animator', 'Ilustrator',
                'Arsitek', 'Interior Designer', 'Fashion Designer',
            ],
            'Hukum' => [
                'Pengacara', 'Notaris', 'Hakim',
                'Jaksa', 'Konsultan Hukum', 'Legal Staff',
            ],
            'Pertanian & Peternakan' => [
                'Agronomist', 'Penyuluh Pertanian', 'Peneliti Pertanian',
                'Peternak', 'Ahli Kehutanan', 'Ahli Perikanan',
            ],
            'Lainnya' => [
                'Wirausaha', 'Freelancer', 'Konsultan Independen',
            ],
        ];

        foreach ($data as $categoryName => $professions) {
            $category = ProfessionCategory::where('name', $categoryName)->first();
            if (! $category) {
                continue;
            }

            foreach ($professions as $professionName) {
                Profession::firstOrCreate(
                    [
                        'profession_category_id' => $category->id,
                        'name'                   => $professionName,
                    ],
                    [
                        'id'        => Str::ulid(),
                        'is_active' => 1,
                    ]
                );
            }
        }
    }
}

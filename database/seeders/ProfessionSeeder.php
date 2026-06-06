<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfessionSeeder extends Seeder
{
    /**
     * Seed profesi berdasarkan kategori yang sudah ada di tabel profession_categories.
     */
    public function run(): void
    {
        $map = [
            'Pendidikan dan Pelatihan' => [
                'Guru / Pengajar',
                'Dosen',
                'Instruktur / Trainer',
                'Kepala Sekolah',
                'Konselor Pendidikan',
            ],
            'Hukum dan Kepatuhan' => [
                'Advokat / Pengacara',
                'Notaris',
                'Hakim',
                'Jaksa',
                'Legal Officer',
                'Compliance Officer',
            ],
            'Ekonomi dan Keuangan' => [
                'Akuntan',
                'Auditor',
                'Analis Keuangan',
                'Bankir',
                'Konsultan Pajak',
                'Manajer Keuangan',
            ],
            'Teknologi dan Informatika' => [
                'Programmer / Software Developer',
                'Data Analyst',
                'System Administrator',
                'UI/UX Designer',
                'Network Engineer',
                'Cyber Security Analyst',
            ],
            'Teknik dan Infrastruktur' => [
                'Insinyur Sipil',
                'Arsitek',
                'Insinyur Mesin',
                'Insinyur Elektro',
                'Quantity Surveyor',
            ],
            'Kesehatan dan Medis' => [
                'Dokter Umum',
                'Dokter Spesialis',
                'Perawat',
                'Apoteker',
                'Bidan',
                'Tenaga Kesehatan Masyarakat',
            ],
            'Agama dan Dakwah' => [
                'Ustadz / Mubaligh',
                'Penyuluh Agama',
                'Imam Masjid',
                'Pegawai KUA',
                'Da\'i',
            ],
            'Pemerintahan dan Administrasi Publik' => [
                'Pegawai Negeri Sipil (PNS)',
                'Aparatur Sipil Negara (ASN)',
                'TNI / Polri',
                'Staf Kelurahan / Kecamatan',
                'Anggota DPRD',
            ],
            'Bisnis dan Wirausaha' => [
                'Wirausahawan',
                'Manajer Pemasaran',
                'Sales Manager',
                'Konsultan Bisnis',
                'E-Commerce Entrepreneur',
            ],
            'Sosial dan Kemasyarakatan' => [
                'Pekerja Sosial',
                'Peneliti Sosial',
                'Aktivis LSM / NGO',
                'Fasilitator Komunitas',
                'Konselor Sosial',
            ],
        ];

        foreach ($map as $categoryName => $professions) {
            $category = DB::table('profession_categories')
                ->where('name', $categoryName)
                ->first();

            if (! $category) {
                continue;
            }

            foreach ($professions as $professionName) {
                DB::table('professions')->insertOrIgnore([
                    'id'                     => Str::ulid(),
                    'profession_category_id' => $category->id,
                    'name'                   => $professionName,
                    'description'            => null,
                    'is_active'              => 1,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);
            }
        }
    }
}

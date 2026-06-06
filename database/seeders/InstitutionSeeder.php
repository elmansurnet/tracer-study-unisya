<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InstitutionSeeder extends Seeder
{
    /**
     * Seed institusi tempat kerja alumni UNISYA.
     */
    public function run(): void
    {
        $institutions = [
            // Perguruan Tinggi & Pendidikan
            ['name' => 'Universitas Islam Syarifuddin (UNISYA)',    'type' => 'swasta',      'sector' => 'Pendidikan Tinggi'],
            ['name' => 'Universitas Negeri Malang (UM)',            'type' => 'pemerintah',  'sector' => 'Pendidikan Tinggi'],
            ['name' => 'Universitas Jember (UNEJ)',                 'type' => 'pemerintah',  'sector' => 'Pendidikan Tinggi'],
            ['name' => 'IAIN Jember',                              'type' => 'pemerintah',  'sector' => 'Pendidikan Tinggi'],
            ['name' => 'Sekolah Menengah Atas Negeri',             'type' => 'pemerintah',  'sector' => 'Pendidikan'],

            // Pemerintahan & BUMN
            ['name' => 'Pemerintah Kabupaten Lumajang',            'type' => 'pemerintah',  'sector' => 'Pemerintahan Daerah'],
            ['name' => 'Pemerintah Kabupaten Jember',              'type' => 'pemerintah',  'sector' => 'Pemerintahan Daerah'],
            ['name' => 'Kantor Kementerian Agama Lumajang',        'type' => 'pemerintah',  'sector' => 'Keagamaan'],
            ['name' => 'Pengadilan Negeri Lumajang',               'type' => 'pemerintah',  'sector' => 'Hukum & Peradilan'],
            ['name' => 'Kantor Urusan Agama (KUA)',                'type' => 'pemerintah',  'sector' => 'Keagamaan'],
            ['name' => 'RSUD Dr. Haryoto Lumajang',                'type' => 'pemerintah',  'sector' => 'Kesehatan'],
            ['name' => 'PT Bank Rakyat Indonesia (BRI)',           'type' => 'bumn',        'sector' => 'Perbankan'],
            ['name' => 'PT Bank Negara Indonesia (BNI)',           'type' => 'bumn',        'sector' => 'Perbankan'],
            ['name' => 'PT Telkom Indonesia',                      'type' => 'bumn',        'sector' => 'Telekomunikasi'],
            ['name' => 'PT PLN (Persero)',                         'type' => 'bumn',        'sector' => 'Energi & Kelistrikan'],

            // Swasta
            ['name' => 'Bank Syariah Indonesia (BSI)',             'type' => 'swasta',      'sector' => 'Perbankan Syariah'],
            ['name' => 'PT Indofood Sukses Makmur',                'type' => 'swasta',      'sector' => 'Manufaktur & FMCG'],
            ['name' => 'CV / PT Lokal Lumajang',                   'type' => 'swasta',      'sector' => 'Umum'],

            // Lainnya / Wirausaha
            ['name' => 'Wirausaha Mandiri',                        'type' => 'lainnya',     'sector' => 'Wirausaha'],
            ['name' => 'LSM / Organisasi Non-Profit',              'type' => 'lainnya',     'sector' => 'Sosial & Kemasyarakatan'],
        ];

        foreach ($institutions as $institution) {
            DB::table('institutions')->insertOrIgnore([
                'id'        => Str::ulid(),
                'name'      => $institution['name'],
                'type'      => $institution['type'],
                'sector'    => $institution['sector'],
                'website'   => null,
                'logo'      => null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

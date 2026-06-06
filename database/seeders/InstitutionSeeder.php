<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\InstitutionDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            [
                'name'   => 'Universitas Islam Syekh Yusuf (UNISYA)',
                'type'   => 'pendidikan',
                'sector' => 'Perguruan Tinggi Swasta',
                'detail' => [
                    'address'  => 'Jl. Multatuli No.188, Tangerang',
                    'city'     => 'Tangerang',
                    'province' => 'Banten',
                ],
            ],
            [
                'name'   => 'PT Pertamina (Persero)',
                'type'   => 'bumn',
                'sector' => 'Energi & Minyak Bumi',
                'detail' => [
                    'city'     => 'Jakarta',
                    'province' => 'DKI Jakarta',
                ],
            ],
            [
                'name'   => 'PT Bank Mandiri (Persero) Tbk',
                'type'   => 'bumn',
                'sector' => 'Perbankan',
                'detail' => [
                    'city'     => 'Jakarta',
                    'province' => 'DKI Jakarta',
                ],
            ],
            [
                'name'   => 'Pemerintah Kota Tangerang',
                'type'   => 'pemerintah',
                'sector' => 'Pemerintah Daerah',
                'detail' => [
                    'city'     => 'Tangerang',
                    'province' => 'Banten',
                ],
            ],
            [
                'name'   => 'PT Telkom Indonesia (Persero) Tbk',
                'type'   => 'bumn',
                'sector' => 'Telekomunikasi',
                'detail' => [
                    'city'     => 'Bandung',
                    'province' => 'Jawa Barat',
                ],
            ],
        ];

        foreach ($institutions as $item) {
            $detail = $item['detail'] ?? [];
            unset($item['detail']);

            $institution = Institution::firstOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['id' => Str::ulid(), 'is_active' => 1])
            );

            if ($detail) {
                InstitutionDetail::updateOrCreate(
                    ['institution_id' => $institution->id],
                    array_merge($detail, ['id' => Str::ulid()])
                );
            }
        }
    }
}

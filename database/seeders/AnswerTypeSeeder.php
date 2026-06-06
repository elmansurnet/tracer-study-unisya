<?php

namespace Database\Seeders;

use App\Models\AnswerType;
use Illuminate\Database\Seeder;

class AnswerTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code'        => AnswerType::TEXT,
                'name'        => 'Teks Pendek',
                'description' => 'Input teks satu baris (max 255 karakter)',
                'config'      => ['max_length' => 255],
                'is_active'   => true,
            ],
            [
                'code'        => AnswerType::TEXTAREA,
                'name'        => 'Teks Panjang',
                'description' => 'Input teks multi-baris (textarea)',
                'config'      => ['rows' => 4],
                'is_active'   => true,
            ],
            [
                'code'        => AnswerType::RADIO,
                'name'        => 'Pilihan Tunggal (Radio)',
                'description' => 'Satu pilihan dari beberapa opsi',
                'config'      => ['options' => []],
                'is_active'   => true,
            ],
            [
                'code'        => AnswerType::CHECKBOX,
                'name'        => 'Pilihan Ganda (Checkbox)',
                'description' => 'Beberapa pilihan dari beberapa opsi',
                'config'      => ['options' => []],
                'is_active'   => true,
            ],
            [
                'code'        => AnswerType::SELECT,
                'name'        => 'Dropdown (Select)',
                'description' => 'Satu pilihan dari daftar dropdown',
                'config'      => ['options' => []],
                'is_active'   => true,
            ],
            [
                'code'        => AnswerType::SCALE,
                'name'        => 'Skala (Likert)',
                'description' => 'Skala penilaian numerik',
                'config'      => ['min' => 1, 'max' => 5, 'labels' => ['Sangat Tidak Setuju', 'Tidak Setuju', 'Netral', 'Setuju', 'Sangat Setuju']],
                'is_active'   => true,
            ],
            [
                'code'        => AnswerType::DATE,
                'name'        => 'Tanggal',
                'description' => 'Input tanggal (date picker)',
                'config'      => null,
                'is_active'   => true,
            ],
            [
                'code'        => AnswerType::NUMBER,
                'name'        => 'Angka',
                'description' => 'Input numerik',
                'config'      => ['min' => null, 'max' => null, 'step' => 1],
                'is_active'   => true,
            ],
        ];

        foreach ($types as $type) {
            AnswerType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}

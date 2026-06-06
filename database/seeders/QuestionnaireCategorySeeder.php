<?php

namespace Database\Seeders;

use App\Models\QuestionnaireCategory;
use Illuminate\Database\Seeder;

class QuestionnaireCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Kepuasan Lulusan',
                'description' => 'Kuesioner untuk mengukur kepuasan alumni terhadap proses pendidikan',
                'is_active'   => true,
            ],
            [
                'name'        => 'Relevansi Kurikulum',
                'description' => 'Kuesioner untuk mengukur relevansi kurikulum dengan kebutuhan dunia kerja',
                'is_active'   => true,
            ],
            [
                'name'        => 'Penilaian Kompetensi',
                'description' => 'Kuesioner penilaian kompetensi alumni oleh pengguna lulusan',
                'is_active'   => true,
            ],
            [
                'name'        => 'Profil Karir',
                'description' => 'Kuesioner untuk menelusuri profil karir dan pekerjaan alumni',
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $category) {
            QuestionnaireCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}

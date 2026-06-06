<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Phase 1
            UserSeeder::class,
            FacultySeeder::class,
            StudyProgramSeeder::class,
            AppSettingSeeder::class,
            // Phase 2
            ProfessionCategorySeeder::class,
            ProfessionSeeder::class,
            InstitutionSeeder::class,
            // Phase 3
            AlumniSeeder::class,
            // Phase 4A
            AnswerTypeSeeder::class,
            QuestionnaireCategorySeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            FacultySeeder::class,
            StudyProgramSeeder::class,
            ProfessionCategorySeeder::class,
            ProfessionSeeder::class,
            InstitutionSeeder::class,
            AppSettingSeeder::class,
        ]);
    }
}

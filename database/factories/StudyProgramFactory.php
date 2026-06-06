<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudyProgramFactory extends Factory
{
    protected $model = StudyProgram::class;

    public function definition(): array
    {
        return [
            'id'         => Str::uuid(),
            'faculty_id' => Faculty::factory(),
            'name'       => $this->faker->unique()->words(4, true),
            'code'       => strtoupper($this->faker->unique()->lexify('????')),
            'degree'     => $this->faker->randomElement(['S1', 'D3', 'S2']),
            'is_active'  => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
<?php

namespace Database\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FacultyFactory extends Factory
{
    protected $model = Faculty::class;

    public function definition(): array
    {
        return [
            'id'         => Str::uuid(),
            'name'       => $this->faker->unique()->words(3, true),
            'code'       => strtoupper($this->faker->unique()->lexify('???')),
            'is_active'  => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
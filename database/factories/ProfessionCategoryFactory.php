<?php

namespace Database\Factories;

use App\Models\ProfessionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProfessionCategoryFactory extends Factory
{
    protected $model = ProfessionCategory::class;

    public function definition(): array
    {
        return [
            'id'          => Str::ulid(),
            'name'        => $this->faker->unique()->words(2, true),
            'description' => $this->faker->optional()->sentence(),
            'is_active'   => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}

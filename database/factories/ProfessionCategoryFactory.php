<?php

namespace Database\Factories;

use App\Models\ProfessionCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProfessionCategoryFactory extends Factory
{
    protected $model = ProfessionCategory::class;

    public function definition(): array
    {
        return [
            'id'          => Str::ulid(),
            'name'        => $this->faker->unique()->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'is_active'   => 1,
        ];
    }
}

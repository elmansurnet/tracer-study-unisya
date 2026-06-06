<?php

namespace Database\Factories;

use App\Models\Profession;
use App\Models\ProfessionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProfessionFactory extends Factory
{
    protected $model = Profession::class;

    public function definition(): array
    {
        return [
            'id'                     => Str::ulid(),
            'profession_category_id' => ProfessionCategory::factory(),
            'name'                   => $this->faker->unique()->jobTitle(),
            'description'            => $this->faker->optional()->sentence(),
            'is_active'              => 1,
        ];
    }
}

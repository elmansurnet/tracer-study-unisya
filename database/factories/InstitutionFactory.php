<?php

namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InstitutionFactory extends Factory
{
    protected $model = Institution::class;

    public function definition(): array
    {
        return [
            'id'        => Str::ulid(),
            'name'      => $this->faker->unique()->company(),
            'type'      => $this->faker->randomElement(['swasta', 'pemerintah', 'bumn', 'lainnya']),
            'sector'    => $this->faker->optional()->word(),
            'website'   => $this->faker->optional()->url(),
            'logo'      => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function swasta(): static
    {
        return $this->state(['type' => 'swasta']);
    }

    public function pemerintah(): static
    {
        return $this->state(['type' => 'pemerintah']);
    }
}

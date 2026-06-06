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
            'type'      => $this->faker->randomElement(['pemerintah', 'swasta', 'bumn', 'pendidikan', 'lainnya']),
            'sector'    => $this->faker->optional()->word(),
            'website'   => $this->faker->optional()->url(),
            'is_active' => 1,
        ];
    }
}

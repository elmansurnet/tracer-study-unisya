<?php

namespace Database\Factories;

use App\Models\QuestionnaireCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireCategoryFactory extends Factory
{
    protected $model = QuestionnaireCategory::class;

    public function definition(): array
    {
        return [
            'name'        => $this->faker->unique()->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'is_active'   => $this->faker->boolean(80),
            'created_by'  => null,
            'updated_by'  => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attr) => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attr) => ['is_active' => false]);
    }
}

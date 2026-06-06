<?php

namespace Database\Factories;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireFactory extends Factory
{
    protected $model = Questionnaire::class;

    public function definition(): array
    {
        return [
            'questionnaire_category_id' => QuestionnaireCategory::factory(),
            'title'                     => $this->faker->sentence(4),
            'description'               => $this->faker->optional()->paragraph(),
            'respondent_type'           => $this->faker->randomElement(Questionnaire::RESPONDENT_TYPES),
            'scope'                     => 'global',
            'faculty_id'                => null,
            'study_program_id'          => null,
            'start_date'                => $this->faker->optional()->dateTimeBetween('-1 month', 'now')?->format('Y-m-d'),
            'end_date'                  => $this->faker->optional()->dateTimeBetween('now', '+6 months')?->format('Y-m-d'),
            'is_active'                 => true,
            'version'                   => 1,
            'created_by'                => null,
            'updated_by'                => null,
        ];
    }

    public function alumni(): static
    {
        return $this->state(fn (array $attr) => ['respondent_type' => 'alumni']);
    }

    public function employer(): static
    {
        return $this->state(fn (array $attr) => ['respondent_type' => 'employer']);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attr) => ['is_active' => false]);
    }
}

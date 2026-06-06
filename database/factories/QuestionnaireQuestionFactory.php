<?php

namespace Database\Factories;

use App\Models\AnswerType;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireQuestionFactory extends Factory
{
    protected $model = QuestionnaireQuestion::class;

    public function definition(): array
    {
        return [
            'questionnaire_id' => Questionnaire::factory(),
            'answer_type_id'   => AnswerType::factory(),
            'question_text'    => $this->faker->sentence() . '?',
            'question_order'   => $this->faker->numberBetween(1, 20),
            'is_required'      => $this->faker->boolean(80),
            'is_active'        => true,
            'created_by'       => null,
            'updated_by'       => null,
        ];
    }

    public function required(): static
    {
        return $this->state(fn (array $attr) => ['is_required' => true]);
    }

    public function optional(): static
    {
        return $this->state(fn (array $attr) => ['is_required' => false]);
    }
}

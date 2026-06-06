<?php

namespace Database\Factories;

use App\Models\AnswerType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerTypeFactory extends Factory
{
    protected $model = AnswerType::class;

    public function definition(): array
    {
        $codes = [AnswerType::TEXT, AnswerType::TEXTAREA, AnswerType::RADIO, AnswerType::CHECKBOX, AnswerType::SELECT, AnswerType::SCALE];

        return [
            'code'        => $this->faker->unique()->randomElement($codes) . '_' . $this->faker->randomNumber(4),
            'name'        => $this->faker->words(2, true),
            'description' => $this->faker->optional()->sentence(),
            'config'      => null,
            'is_active'   => true,
        ];
    }
}

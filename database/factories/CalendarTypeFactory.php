<?php

namespace Database\Factories;

use App\Models\CalendarType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarTypeFactory extends Factory
{
    protected $model = CalendarType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}

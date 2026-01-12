<?php

namespace Database\Factories;

use App\Models\CalendarAction;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarActionFactory extends Factory
{
    protected $model = CalendarAction::class;

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

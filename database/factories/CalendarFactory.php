<?php

namespace Database\Factories;

use App\Models\Calendar;
use App\Models\CalendarAction;
use App\Models\CalendarType;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarFactory extends Factory
{
    protected $model = Calendar::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate = (clone $startDate)->modify('+1 hour');

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'all_day' => false,
            'calendar_type_id' => CalendarType::factory(),
            'calendar_action_id' => CalendarAction::factory(),
            'entity_id' => Entity::factory(),
            'user_id' => User::factory(),
            'color' => $this->faker->hexColor(),
            'notes' => $this->faker->text(),
        ];
    }

    public function allDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'all_day' => true,
        ]);
    }
}

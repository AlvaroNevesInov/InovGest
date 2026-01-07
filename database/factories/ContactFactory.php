<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $number = 1;

        return [
            'number' => $number++,
            'entity_id' => \App\Models\Entity::factory(),
            'name' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'contact_function_id' => \App\Models\ContactFunction::factory(),
            'phone' => fake()->numerify('2## ### ###'),
            'mobile' => fake()->numerify('9## ### ###'),
            'email' => fake()->email(),
            'rgpd_consent' => fake()->boolean(80),
            'notes' => fake()->optional()->sentence(),
            'active' => true,
        ];
    }

    /**
     * Indicate that the contact is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}

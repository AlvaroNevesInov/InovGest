<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity>
 */
class EntityFactory extends Factory
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
            'type' => fake()->randomElement(['client', 'supplier', 'both']),
            'number' => $number++,
            'nif' => fake()->numerify('#########'),
            'name' => fake()->company(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->numerify('####-###'),
            'city' => fake()->city(),
            'country_id' => \App\Models\Country::factory(),
            'phone' => fake()->numerify('2## ### ###'),
            'mobile' => fake()->numerify('9## ### ###'),
            'website' => fake()->url(),
            'email' => fake()->companyEmail(),
            'rgpd_consent' => fake()->boolean(80),
            'notes' => fake()->optional()->sentence(),
            'active' => true,
        ];
    }

    /**
     * Indicate that the entity is a client.
     */
    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'client',
        ]);
    }

    /**
     * Indicate that the entity is a supplier.
     */
    public function supplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'supplier',
        ]);
    }

    /**
     * Indicate that the entity is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}

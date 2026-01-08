<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VatRate>
 */
class VatRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rates = [
            ['name' => 'IVA Normal (23%)', 'rate' => 23.00],
            ['name' => 'IVA Reduzido (13%)', 'rate' => 13.00],
            ['name' => 'IVA Intermédio (6%)', 'rate' => 6.00],
            ['name' => 'Isento', 'rate' => 0.00],
        ];

        $rate = fake()->randomElement($rates);

        return [
            'name' => $rate['name'],
            'rate' => $rate['rate'],
            'active' => true,
        ];
    }

    /**
     * Indicate that the VAT rate is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}

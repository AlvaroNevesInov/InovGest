<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $refNumber = 1;

        $productNames = [
            'Computador Portátil', 'Monitor LED', 'Teclado Mecânico', 'Rato Wireless',
            'Webcam HD', 'Headset', 'Impressora Laser', 'Scanner', 'Pen USB',
            'Disco Externo', 'Router WiFi', 'Switch de Rede', 'Cabo HDMI',
            'Adaptador USB-C', 'Hub USB', 'Cadeira de Escritório', 'Secretária',
            'Candeeiro LED', 'Quadro Branco', 'Destruidor de Papel'
        ];

        return [
            'reference' => 'ART-' . str_pad($refNumber++, 5, '0', STR_PAD_LEFT),
            'name' => fake()->randomElement($productNames),
            'description' => fake()->optional(0.7)->paragraph(),
            'price' => fake()->randomFloat(2, 10, 2000),
            'vat_rate_id' => \App\Models\VatRate::factory(),
            'photo' => null,
            'notes' => fake()->optional(0.3)->sentence(),
            'active' => true,
        ];
    }

    /**
     * Indicate that the article is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}

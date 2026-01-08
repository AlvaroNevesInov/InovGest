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
        static $refNumber = null;
        static $initialized = false;

        // Initialize refNumber from database only once
        if (!$initialized) {
            $lastArticle = \App\Models\Article::orderBy('id', 'desc')->first();
            if ($lastArticle && preg_match('/ART-(\d+)/', $lastArticle->reference, $matches)) {
                $refNumber = (int)$matches[1];
            } else {
                $refNumber = 0;
            }
            $initialized = true;
        }

        $refNumber++;

        $productNames = [
            'Computador Portátil', 'Monitor LED', 'Teclado Mecânico', 'Rato Wireless',
            'Webcam HD', 'Headset', 'Impressora Laser', 'Scanner', 'Pen USB',
            'Disco Externo', 'Router WiFi', 'Switch de Rede', 'Cabo HDMI',
            'Adaptador USB-C', 'Hub USB', 'Cadeira de Escritório', 'Secretária',
            'Candeeiro LED', 'Quadro Branco', 'Destruidor de Papel'
        ];

        // Try to use existing VAT rates, otherwise create one
        $vatRateId = \App\Models\VatRate::inRandomOrder()->first()?->id
                     ?? \App\Models\VatRate::factory()->create()->id;

        return [
            'reference' => 'ART-' . str_pad($refNumber, 5, '0', STR_PAD_LEFT),
            'name' => fake()->randomElement($productNames),
            'description' => fake()->optional(0.7)->paragraph(),
            'price' => fake()->randomFloat(2, 10, 2000),
            'vat_rate_id' => $vatRateId,
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

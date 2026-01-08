<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Entity;
use App\Models\Order;
use App\Models\VatRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderLine>
 */
class OrderLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random article
        $article = Article::inRandomOrder()->first()
                   ?? Article::factory()->create();

        // Get a random supplier
        $supplier = Entity::suppliers()->inRandomOrder()->first();

        $quantity = fake()->randomFloat(2, 1, 100);
        $unitPrice = $article->price;
        $discountPercentage = fake()->optional(0.3)->randomFloat(2, 0, 20) ?? 0;
        $vatRate = $article->vatRate ? $article->vatRate->rate : 0;

        // Calculate totals
        $subtotalBeforeDiscount = $quantity * $unitPrice;
        $discount = $discountPercentage > 0 ? $subtotalBeforeDiscount * ($discountPercentage / 100) : 0;
        $subtotal = $subtotalBeforeDiscount - $discount;
        $taxAmount = $subtotal * ($vatRate / 100);
        $total = $subtotal + $taxAmount;

        return [
            'order_id' => Order::factory(),
            'article_id' => $article->id,
            'article_reference' => $article->reference,
            'article_name' => $article->name,
            'description' => $article->description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percentage' => $discountPercentage,
            'discount' => $discount,
            'vat_rate_id' => $article->vat_rate_id,
            'vat_rate' => $vatRate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'supplier_id' => $supplier?->id,
            'cost_price' => $supplier ? fake()->randomFloat(2, $unitPrice * 0.5, $unitPrice * 0.8) : null,
            'sort_order' => 0,
        ];
    }
}

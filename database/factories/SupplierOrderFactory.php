<?php

namespace Database\Factories;

use App\Models\Entity;
use App\Models\SupplierOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierOrderFactory extends Factory
{
    protected $model = SupplierOrder::class;

    public function definition(): array
    {
        return [
            'number' => 'SF' . fake()->unique()->numberBetween(100000, 999999),
            'order_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'supplier_id' => Entity::factory()->supplier(),
            'order_id' => null,
            'status' => 'draft',
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }
}

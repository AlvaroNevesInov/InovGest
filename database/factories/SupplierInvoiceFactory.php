<?php

namespace Database\Factories;

use App\Models\Entity;
use App\Models\SupplierInvoice;
use App\Models\SupplierOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierInvoiceFactory extends Factory
{
    protected $model = SupplierInvoice::class;

    public function definition(): array
    {
        $invoiceDate = fake()->dateTimeBetween('-2 months', 'now');
        $dueDate = fake()->dateTimeBetween($invoiceDate, '+1 month');

        return [
            'number' => 'INV-' . fake()->unique()->numberBetween(100000, 999999),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'supplier_id' => Entity::factory()->supplier(),
            'supplier_order_id' => null,
            'total_amount' => fake()->randomFloat(2, 100, 10000),
            'document_path' => null,
            'payment_proof_path' => null,
            'status' => 'pending',
            'payment_date' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'payment_date' => fake()->dateTimeBetween($attributes['invoice_date'], 'now'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_date' => null,
        ]);
    }
}

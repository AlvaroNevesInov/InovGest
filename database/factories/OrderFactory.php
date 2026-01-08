<?php

namespace Database\Factories;

use App\Models\Entity;
use App\Models\Proposal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderDate = fake()->dateTimeBetween('-2 months', 'now');

        // Get a random client entity
        $client = Entity::clients()->inRandomOrder()->first()
                  ?? Entity::factory()->client()->create();

        // Get next order number
        $nextNumber = (\App\Models\Order::max('number') ?? 0) + 1;

        return [
            'number' => $nextNumber,
            'order_date' => $orderDate,
            'entity_id' => $client->id,
            'proposal_id' => null, // Can be set explicitly
            'status' => fake()->randomElement(['draft', 'closed']),
            'notes' => fake()->optional(0.3)->paragraph(),
            'subtotal' => 0,
            'tax_total' => 0,
            'total' => 0,
        ];
    }

    /**
     * Indicate that the order is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the order is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }

    /**
     * Indicate that the order is from a proposal.
     */
    public function fromProposal(Proposal $proposal): static
    {
        return $this->state(fn (array $attributes) => [
            'proposal_id' => $proposal->id,
            'entity_id' => $proposal->entity_id,
            'order_date' => now(),
        ]);
    }
}

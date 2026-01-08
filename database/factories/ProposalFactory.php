<?php

namespace Database\Factories;

use App\Models\Entity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $proposalDate = fake()->dateTimeBetween('-3 months', 'now');
        $validityDate = Carbon::parse($proposalDate)->addDays(30);

        // Get a random client entity
        $client = Entity::clients()->inRandomOrder()->first()
                  ?? Entity::factory()->client()->create();

        // Get next proposal number
        $nextNumber = (\App\Models\Proposal::max('number') ?? 0) + 1;

        return [
            'number' => $nextNumber,
            'proposal_date' => $proposalDate,
            'entity_id' => $client->id,
            'validity_date' => $validityDate,
            'status' => fake()->randomElement(['draft', 'closed']),
            'notes' => fake()->optional(0.3)->paragraph(),
            'subtotal' => 0,
            'tax_total' => 0,
            'total' => 0,
        ];
    }

    /**
     * Indicate that the proposal is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the proposal is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }

    /**
     * Indicate that the proposal is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'proposal_date' => fake()->dateTimeBetween('-6 months', '-2 months'),
            'validity_date' => fake()->dateTimeBetween('-5 months', '-1 month'),
        ]);
    }
}

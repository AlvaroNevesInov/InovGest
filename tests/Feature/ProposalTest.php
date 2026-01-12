<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Entity;
use App\Models\Proposal;
use App\Models\User;
use App\Models\VatRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Entity $client;
    protected Article $article;
    protected VatRate $vatRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->client = Entity::factory()->client()->create();
        $this->vatRate = VatRate::factory()->create();
        $this->article = Article::factory()->create([
            'vat_rate_id' => $this->vatRate->id,
        ]);
    }

    /** @test */
    public function it_displays_proposals_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('proposals.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_proposal()
    {
        $proposalData = [
            'proposal_date' => now()->toDateString(),
            'entity_id' => $this->client->id,
            'validity_date' => now()->addDays(30)->toDateString(),
            'notes' => 'Test proposal notes',
            'lines' => [
                [
                    'article_id' => $this->article->id,
                    'article_name' => $this->article->name,
                    'description' => 'Test line',
                    'quantity' => 2,
                    'unit_price' => 100.00,
                    'discount' => 0,
                    'discount_percentage' => 0,
                    'vat_rate_id' => $this->vatRate->id,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('proposals.store'), $proposalData);

        $response->assertRedirect();

        $this->assertDatabaseHas('proposals', [
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('proposals.store'), []);

        $response->assertSessionHasErrors(['proposal_date', 'entity_id', 'validity_date', 'lines']);
    }

    /** @test */
    public function it_calculates_totals_correctly()
    {
        $proposalData = [
            'proposal_date' => now()->toDateString(),
            'entity_id' => $this->client->id,
            'validity_date' => now()->addDays(30)->toDateString(),
            'lines' => [
                [
                    'article_id' => $this->article->id,
                    'article_name' => $this->article->name,
                    'description' => 'Test line',
                    'quantity' => 2,
                    'unit_price' => 100.00,
                    'discount' => 0,
                    'discount_percentage' => 0,
                    'vat_rate_id' => $this->vatRate->id,
                ],
            ],
        ];

        $this->actingAs($this->user)
            ->post(route('proposals.store'), $proposalData);

        $proposal = Proposal::first();

        $this->assertEquals(200.00, $proposal->subtotal);
    }

    /** @test */
    public function it_can_close_a_proposal()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('proposals.close', $proposal));

        $response->assertRedirect();

        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => 'closed',
        ]);
    }

    /** @test */
    public function it_cannot_edit_closed_proposal()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('proposals.edit', $proposal));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_can_convert_proposal_to_order()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'closed',
        ]);

        $proposal->lines()->create([
            'article_id' => $this->article->id,
            'article_name' => $this->article->name,
            'quantity' => 1,
            'unit_price' => 100.00,
            'vat_rate_id' => $this->vatRate->id,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('proposals.convertToOrder', $proposal));

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'entity_id' => $this->client->id,
        ]);
    }

    public function test_it_can_generate_pdf()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
        ]);

        $proposal->lines()->create([
            'article_id' => $this->article->id,
            'article_name' => $this->article->name,
            'quantity' => 1,
            'unit_price' => 100.00,
            'vat_rate_id' => $this->vatRate->id,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('proposals.pdf', $proposal));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_it_can_delete_draft_proposal()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('proposals.destroy', $proposal));

        $response->assertRedirect(route('proposals.index'));

        $this->assertDatabaseMissing('proposals', [
            'id' => $proposal->id,
        ]);
    }

    public function test_it_cannot_delete_closed_proposal()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('proposals.destroy', $proposal));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
        ]);
    }

    public function test_it_filters_proposals_by_status()
    {
        Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('proposals.index', ['status' => 'draft']));

        $response->assertStatus(200);
    }

    public function test_unauthenticated_users_cannot_access_proposals()
    {
        $response = $this->get(route('proposals.index'));

        $response->assertRedirect(route('login'));
    }
}

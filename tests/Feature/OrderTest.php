<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Entity;
use App\Models\Order;
use App\Models\User;
use App\Models\VatRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Entity $client;
    protected Entity $supplier;
    protected Article $article;
    protected VatRate $vatRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->client = Entity::factory()->client()->create();
        $this->supplier = Entity::factory()->supplier()->create();
        $this->vatRate = VatRate::factory()->create();
        $this->article = Article::factory()->create([
            'vat_rate_id' => $this->vatRate->id,
        ]);
    }

    public function test_it_displays_orders_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('orders.index'));

        $response->assertStatus(200);
    }

    public function test_it_can_create_an_order()
    {
        $orderData = [
            'order_date' => now()->toDateString(),
            'entity_id' => $this->client->id,
            'notes' => 'Test order notes',
            'lines' => [
                [
                    'article_id' => $this->article->id,
                    'article_name' => $this->article->name,
                    'description' => 'Test line',
                    'quantity' => 3,
                    'unit_price' => 150.00,
                    'discount' => 0,
                    'discount_percentage' => 0,
                    'vat_rate_id' => $this->vatRate->id,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('orders.store'), $orderData);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);
    }

    public function test_it_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('orders.store'), []);

        $response->assertSessionHasErrors(['order_date', 'entity_id', 'lines']);
    }

    public function test_it_calculates_totals_correctly()
    {
        $orderData = [
            'order_date' => now()->toDateString(),
            'entity_id' => $this->client->id,
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
            ->post(route('orders.store'), $orderData);

        $order = Order::first();

        $this->assertEquals(200.00, $order->subtotal);
    }

    public function test_it_can_close_an_order()
    {
        $order = Order::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('orders.close', $order));

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'closed',
        ]);
    }

    public function test_it_cannot_edit_closed_order()
    {
        $order = Order::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('orders.edit', $order));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_it_can_convert_order_to_supplier_orders()
    {
        $order = Order::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'closed',
        ]);

        // Add lines with suppliers
        $order->lines()->create([
            'article_id' => $this->article->id,
            'article_name' => $this->article->name,
            'quantity' => 1,
            'unit_price' => 100.00,
            'vat_rate_id' => $this->vatRate->id,
            'supplier_id' => $this->supplier->id,
            'cost_price' => 80.00,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('orders.convertToSupplierOrders', $order));

        $response->assertRedirect();

        // Verify supplier order was created
        $this->assertDatabaseHas('supplier_orders', [
            'supplier_id' => $this->supplier->id,
        ]);
    }

    public function test_it_can_generate_pdf()
    {
        $order = Order::factory()->create([
            'entity_id' => $this->client->id,
        ]);

        $order->lines()->create([
            'article_id' => $this->article->id,
            'article_name' => $this->article->name,
            'quantity' => 1,
            'unit_price' => 100.00,
            'vat_rate_id' => $this->vatRate->id,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('orders.pdf', $order));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_it_can_delete_draft_order()
    {
        $order = Order::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('orders.destroy', $order));

        $response->assertRedirect(route('orders.index'));

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }

    public function test_it_cannot_delete_closed_order()
    {
        $order = Order::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('orders.destroy', $order));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
    }

    public function test_it_filters_orders_by_status()
    {
        Order::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        Order::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('orders.index', ['status' => 'draft']));

        $response->assertStatus(200);
    }

    public function test_unauthenticated_users_cannot_access_orders()
    {
        $response = $this->get(route('orders.index'));

        $response->assertRedirect(route('login'));
    }
}

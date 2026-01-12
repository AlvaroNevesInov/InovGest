<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Entity;
use App\Models\Order;
use App\Models\Proposal;
use App\Models\SupplierOrder;
use App\Models\User;
use App\Models\VatRate;
use App\Services\ProposalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalToOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Entity $client;
    protected Entity $supplier1;
    protected Entity $supplier2;
    protected Article $article1;
    protected Article $article2;
    protected VatRate $vatRate;
    protected ProposalService $proposalService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->client = Entity::factory()->client()->create();
        $this->supplier1 = Entity::factory()->supplier()->create(['name' => 'Supplier A']);
        $this->supplier2 = Entity::factory()->supplier()->create(['name' => 'Supplier B']);
        $this->vatRate = VatRate::factory()->create(['rate' => 23]);
        $this->article1 = Article::factory()->create(['vat_rate_id' => $this->vatRate->id]);
        $this->article2 = Article::factory()->create(['vat_rate_id' => $this->vatRate->id]);
        $this->proposalService = new ProposalService();
    }

    public function test_complete_flow_from_proposal_to_supplier_orders()
    {
        // 1. Create a proposal with multiple lines
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $proposal->lines()->create([
            'article_id' => $this->article1->id,
            'article_name' => $this->article1->name,
            'quantity' => 10,
            'unit_price' => 100.00,
            'vat_rate_id' => $this->vatRate->id,
            'supplier_id' => $this->supplier1->id,
            'cost_price' => 80.00,
            'sort_order' => 0,
        ]);

        $proposal->lines()->create([
            'article_id' => $this->article2->id,
            'article_name' => $this->article2->name,
            'quantity' => 5,
            'unit_price' => 200.00,
            'vat_rate_id' => $this->vatRate->id,
            'supplier_id' => $this->supplier2->id,
            'cost_price' => 150.00,
            'sort_order' => 1,
        ]);

        $proposal->calculateTotals();
        $proposal->refresh();

        // Store original totals
        $originalSubtotal = $proposal->subtotal;
        $originalTotal = $proposal->total;

        $this->assertEquals(2000.00, $originalSubtotal); // 10*100 + 5*200 = 2000

        // 2. Close the proposal (but don't call close() manually - convertToOrder will do it)
        $proposal->update(['status' => 'closed']);
        $proposal->refresh();

        $this->assertEquals('closed', $proposal->status);

        // 3. Convert proposal to order
        $order = $this->proposalService->convertToOrder($proposal);
        $order->refresh();

        // Verify order was created with correct data
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($this->client->id, $order->entity_id);
        $this->assertEquals($proposal->id, $order->proposal_id);
        $this->assertEquals('draft', $order->status);

        // Verify order lines were copied
        $this->assertEquals(2, $order->lines()->count());

        $orderLine1 = $order->lines()->where('article_id', $this->article1->id)->first();
        $this->assertEquals(10, $orderLine1->quantity);
        $this->assertEquals(100.00, $orderLine1->unit_price);
        $this->assertEquals($this->supplier1->id, $orderLine1->supplier_id);
        $this->assertEquals(80.00, $orderLine1->cost_price);

        $orderLine2 = $order->lines()->where('article_id', $this->article2->id)->first();
        $this->assertEquals(5, $orderLine2->quantity);
        $this->assertEquals(200.00, $orderLine2->unit_price);
        $this->assertEquals($this->supplier2->id, $orderLine2->supplier_id);
        $this->assertEquals(150.00, $orderLine2->cost_price);

        // Verify totals match
        $this->assertEquals($originalSubtotal, $order->subtotal);
        $this->assertEquals($originalTotal, $order->total);

        // 4. Close the order
        $order->close();
        $order->refresh();

        $this->assertEquals('closed', $order->status);

        // 5. Create supplier orders from the order
        $supplierOrders = $this->proposalService->createSupplierOrders($order);

        // Verify supplier orders were created (they are regular Orders with supplier as entity)
        $this->assertCount(2, $supplierOrders);

        // Verify first supplier order (Supplier A)
        $supplierOrder1 = $supplierOrders[0];
        $supplierOrder1->refresh();
        $this->assertInstanceOf(Order::class, $supplierOrder1);
        $this->assertEquals($this->supplier1->id, $supplierOrder1->entity_id);
        $this->assertEquals('draft', $supplierOrder1->status);

        $supplierOrder1Lines = $supplierOrder1->lines;
        $this->assertCount(1, $supplierOrder1Lines);
        $this->assertEquals($this->article1->id, $supplierOrder1Lines[0]->article_id);
        $this->assertEquals(10, $supplierOrder1Lines[0]->quantity);
        $this->assertEquals(80.00, $supplierOrder1Lines[0]->unit_price); // Should use cost_price

        // Verify second supplier order (Supplier B)
        $supplierOrder2 = $supplierOrders[1];
        $supplierOrder2->refresh();
        $this->assertInstanceOf(Order::class, $supplierOrder2);
        $this->assertEquals($this->supplier2->id, $supplierOrder2->entity_id);

        $supplierOrder2Lines = $supplierOrder2->lines;
        $this->assertCount(1, $supplierOrder2Lines);
        $this->assertEquals($this->article2->id, $supplierOrder2Lines[0]->article_id);
        $this->assertEquals(5, $supplierOrder2Lines[0]->quantity);
        $this->assertEquals(150.00, $supplierOrder2Lines[0]->unit_price); // Should use cost_price
    }

    public function test_order_without_suppliers_throws_exception()
    {
        // Create a proposal with lines without suppliers
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $proposal->lines()->create([
            'article_id' => $this->article1->id,
            'article_name' => $this->article1->name,
            'quantity' => 10,
            'unit_price' => 100.00,
            'vat_rate_id' => $this->vatRate->id,
            'supplier_id' => null, // No supplier
            'sort_order' => 0,
        ]);

        $proposal->calculateTotals();
        $proposal->update(['status' => 'closed']);

        $order = $this->proposalService->convertToOrder($proposal);
        $order->close();

        // Should throw exception when no suppliers
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Nenhuma linha com fornecedor definido');

        $this->proposalService->createSupplierOrders($order);
    }

    public function test_proposal_cannot_be_converted_if_not_closed()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft', // Not closed
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A proposta precisa estar fechada para ser convertida');

        $this->proposalService->convertToOrder($proposal);
    }

    public function test_proposal_can_be_converted_to_order()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $proposal->lines()->create([
            'article_id' => $this->article1->id,
            'article_name' => $this->article1->name,
            'quantity' => 10,
            'unit_price' => 100.00,
            'vat_rate_id' => $this->vatRate->id,
            'sort_order' => 0,
        ]);

        $proposal->calculateTotals();
        $proposal->update(['status' => 'closed']);

        // Conversion should work
        $order = $this->proposalService->convertToOrder($proposal);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($proposal->id, $order->proposal_id);
        $this->assertEquals($this->client->id, $order->entity_id);
    }

    public function test_multiple_lines_for_same_supplier_are_grouped()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        // Create multiple lines for the same supplier
        $proposal->lines()->create([
            'article_id' => $this->article1->id,
            'article_name' => $this->article1->name,
            'quantity' => 10,
            'unit_price' => 100.00,
            'vat_rate_id' => $this->vatRate->id,
            'supplier_id' => $this->supplier1->id,
            'cost_price' => 80.00,
            'sort_order' => 0,
        ]);

        $proposal->lines()->create([
            'article_id' => $this->article2->id,
            'article_name' => $this->article2->name,
            'quantity' => 5,
            'unit_price' => 200.00,
            'vat_rate_id' => $this->vatRate->id,
            'supplier_id' => $this->supplier1->id, // Same supplier
            'cost_price' => 150.00,
            'sort_order' => 1,
        ]);

        $proposal->calculateTotals();
        $proposal->update(['status' => 'closed']);

        $order = $this->proposalService->convertToOrder($proposal);
        $order->close();

        $supplierOrders = $this->proposalService->createSupplierOrders($order);

        // Should create only 1 supplier order with 2 lines
        $this->assertCount(1, $supplierOrders);

        $supplierOrder = $supplierOrders[0];
        $this->assertEquals($this->supplier1->id, $supplierOrder->entity_id);
        $this->assertCount(2, $supplierOrder->lines);
    }

    public function test_totals_are_preserved_through_entire_flow()
    {
        $proposal = Proposal::factory()->create([
            'entity_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $proposal->lines()->create([
            'article_id' => $this->article1->id,
            'article_name' => $this->article1->name,
            'quantity' => 10,
            'unit_price' => 100.00,
            'discount' => 50.00,
            'vat_rate_id' => $this->vatRate->id,
            'supplier_id' => $this->supplier1->id,
            'cost_price' => 80.00,
            'sort_order' => 0,
        ]);

        $proposal->calculateTotals();
        $originalSubtotal = $proposal->subtotal;
        $originalDiscount = $proposal->discount;
        $originalTaxable = $proposal->taxable_amount;
        $originalVat = $proposal->vat_amount;
        $originalTotal = $proposal->total;

        $proposal->update(['status' => 'closed']);
        $order = $this->proposalService->convertToOrder($proposal);

        // Verify all totals match
        $this->assertEquals($originalSubtotal, $order->subtotal);
        $this->assertEquals($originalDiscount, $order->discount);
        $this->assertEquals($originalTaxable, $order->taxable_amount);
        $this->assertEquals($originalVat, $order->vat_amount);
        $this->assertEquals($originalTotal, $order->total);
    }
}

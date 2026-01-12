<?php

namespace Tests\Feature;

use App\Models\Entity;
use App\Models\SupplierInvoice;
use App\Models\SupplierOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupplierInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Entity $supplier;
    protected SupplierOrder $supplierOrder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->supplier = Entity::factory()->supplier()->create();
        $this->supplierOrder = SupplierOrder::factory()->create([
            'supplier_id' => $this->supplier->id,
        ]);
    }

    public function test_it_displays_supplier_invoices_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('supplier-invoices.index'));

        $response->assertStatus(200);
    }

    public function test_it_can_create_a_supplier_invoice()
    {
        Storage::fake('documents');

        $invoiceData = [
            'number' => 'INV-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'supplier_order_id' => $this->supplierOrder->id,
            'total_amount' => 1000.00,
            'notes' => 'Test invoice',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.store'), $invoiceData);

        $response->assertRedirect();

        $this->assertDatabaseHas('supplier_invoices', [
            'number' => 'INV-001',
            'supplier_id' => $this->supplier->id,
            'status' => 'pending',
        ]);
    }

    public function test_it_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.store'), []);

        $response->assertSessionHasErrors(['number', 'invoice_date', 'due_date', 'supplier_id', 'total_amount']);
    }

    public function test_it_validates_due_date_after_invoice_date()
    {
        $invoiceData = [
            'number' => 'INV-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->subDays(1)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'total_amount' => 1000.00,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.store'), $invoiceData);

        $response->assertSessionHasErrors(['due_date']);
    }

    public function test_it_validates_unique_invoice_number()
    {
        SupplierInvoice::factory()->create([
            'number' => 'INV-001',
            'supplier_id' => $this->supplier->id,
        ]);

        $invoiceData = [
            'number' => 'INV-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'total_amount' => 1000.00,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.store'), $invoiceData);

        $response->assertSessionHasErrors(['number']);
    }

    public function test_it_can_upload_invoice_document()
    {
        Storage::fake('documents');

        $file = UploadedFile::fake()->create('invoice.pdf', 1000, 'application/pdf');

        $invoiceData = [
            'number' => 'INV-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'total_amount' => 1000.00,
            'document' => $file,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.store'), $invoiceData);

        $response->assertRedirect();

        $invoice = SupplierInvoice::where('number', 'INV-001')->first();
        $this->assertNotNull($invoice->document_path);
        $this->assertTrue(Storage::disk('documents')->exists($invoice->document_path));
    }

    public function test_it_can_update_supplier_invoice()
    {
        $invoice = SupplierInvoice::factory()->create([
            'supplier_id' => $this->supplier->id,
            'number' => 'INV-001',
        ]);

        $updateData = [
            'number' => 'INV-001-UPDATED',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'total_amount' => 2000.00,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('supplier-invoices.update', $invoice), $updateData);

        $response->assertRedirect();

        $this->assertDatabaseHas('supplier_invoices', [
            'id' => $invoice->id,
            'number' => 'INV-001-UPDATED',
            'total_amount' => 2000.00,
        ]);
    }

    public function test_it_can_delete_supplier_invoice()
    {
        Storage::fake('documents');

        $invoice = SupplierInvoice::factory()->create([
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('supplier-invoices.destroy', $invoice));

        $response->assertRedirect(route('supplier-invoices.index'));

        $this->assertDatabaseMissing('supplier_invoices', [
            'id' => $invoice->id,
        ]);
    }

    public function test_it_can_mark_invoice_as_paid()
    {
        $invoice = SupplierInvoice::factory()->pending()->create([
            'supplier_id' => $this->supplier->id,
        ]);

        $paymentData = [
            'payment_date' => now()->toDateString(),
            'send_email' => false,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.markAsPaid', $invoice), $paymentData);

        $response->assertRedirect();

        $this->assertDatabaseHas('supplier_invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
    }

    public function test_it_can_upload_payment_proof_when_marking_as_paid()
    {
        Storage::fake('documents');

        $invoice = SupplierInvoice::factory()->pending()->create([
            'supplier_id' => $this->supplier->id,
        ]);

        $file = UploadedFile::fake()->create('payment_proof.pdf', 1000, 'application/pdf');

        $paymentData = [
            'payment_date' => now()->toDateString(),
            'payment_proof' => $file,
            'send_email' => false,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.markAsPaid', $invoice), $paymentData);

        $response->assertRedirect();

        $invoice->refresh();
        $this->assertNotNull($invoice->payment_proof_path);
        $this->assertTrue(Storage::disk('documents')->exists($invoice->payment_proof_path));
    }

    public function test_it_sends_email_when_marking_as_paid_with_email_flag()
    {
        Mail::fake();
        Storage::fake('documents');

        $invoice = SupplierInvoice::factory()->pending()->create([
            'supplier_id' => $this->supplier->id,
        ]);

        $paymentData = [
            'payment_date' => now()->toDateString(),
            'send_email' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.markAsPaid', $invoice), $paymentData);

        $response->assertRedirect();

        Mail::assertSent(\App\Mail\PaymentProofMail::class, function ($mail) use ($invoice) {
            return $mail->hasTo($invoice->supplier->email);
        });
    }

    public function test_it_filters_invoices_by_status()
    {
        SupplierInvoice::factory()->pending()->create([
            'supplier_id' => $this->supplier->id,
        ]);

        SupplierInvoice::factory()->paid()->create([
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('supplier-invoices.index', ['status' => 'pending']));

        $response->assertStatus(200);
    }

    public function test_it_searches_invoices_by_number()
    {
        SupplierInvoice::factory()->create([
            'number' => 'INV-SEARCH-001',
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('supplier-invoices.index', ['search' => 'SEARCH']));

        $response->assertStatus(200);
    }

    public function test_unauthenticated_users_cannot_access_supplier_invoices()
    {
        $response = $this->get(route('supplier-invoices.index'));

        $response->assertRedirect(route('login'));
    }
}

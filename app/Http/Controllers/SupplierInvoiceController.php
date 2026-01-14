<?php

namespace App\Http\Controllers;

use App\Models\SupplierInvoice;
use App\Models\Entity;
use App\Models\SupplierOrder;
use App\Mail\PaymentProofMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class SupplierInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierInvoice::with(['supplier', 'supplierOrder']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();

        return Inertia::render('SupplierInvoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:255|unique:supplier_invoices,number',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'supplier_id' => 'required|exists:entities,id',
            'supplier_order_id' => 'nullable|exists:supplier_orders,id',
            'total_amount' => 'required|numeric|min:0',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')->store('supplier-invoices', 'documents');
        }

        SupplierInvoice::create($validated);

        return back()->with('success', 'Fatura de fornecedor criada com sucesso!');
    }

    public function update(Request $request, SupplierInvoice $supplierInvoice)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:255|unique:supplier_invoices,number,' . $supplierInvoice->id,
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'supplier_id' => 'required|exists:entities,id',
            'supplier_order_id' => 'nullable|exists:supplier_orders,id',
            'total_amount' => 'required|numeric|min:0',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('document')) {
            // Delete old document
            if ($supplierInvoice->document_path) {
                Storage::disk('documents')->delete($supplierInvoice->document_path);
            }
            $validated['document_path'] = $request->file('document')->store('supplier-invoices', 'documents');
        }

        $supplierInvoice->update($validated);

        return back()->with('success', 'Fatura de fornecedor atualizada com sucesso!');
    }

    public function destroy(SupplierInvoice $supplierInvoice)
    {
        // Delete documents
        if ($supplierInvoice->document_path) {
            Storage::disk('documents')->delete($supplierInvoice->document_path);
        }
        if ($supplierInvoice->payment_proof_path) {
            Storage::disk('documents')->delete($supplierInvoice->payment_proof_path);
        }

        $supplierInvoice->delete();

        return redirect()->route('supplier-invoices.index')->with('success', 'Fatura de fornecedor eliminada com sucesso!');
    }

    public function markAsPaid(Request $request, SupplierInvoice $supplierInvoice)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'send_email' => 'boolean',
        ]);

        if ($request->hasFile('payment_proof')) {
            $validated['payment_proof_path'] = $request->file('payment_proof')->store('payment-proofs', 'documents');
        }

        $supplierInvoice->update([
            'status' => 'paid',
            'payment_date' => $validated['payment_date'],
            'payment_proof_path' => $validated['payment_proof_path'] ?? $supplierInvoice->payment_proof_path,
        ]);

        // Send email if requested
        if ($request->send_email && $supplierInvoice->supplier->email) {
            try {
                // Load supplier relationship for email
                $supplierInvoice->load('supplier');

                Mail::to($supplierInvoice->supplier->email)->send(
                    new PaymentProofMail($supplierInvoice)
                );
                return back()->with('success', 'Fatura marcada como paga e email enviado com sucesso!');
            } catch (\Exception $e) {
                \Log::error('Failed to send payment proof email: ' . $e->getMessage());
                return back()->with('warning', 'Fatura marcada como paga mas o email não pôde ser enviado: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Fatura marcada como paga com sucesso!');
    }

    /**
     * Download invoice document or payment proof
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SupplierInvoice $supplierInvoice
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(Request $request, SupplierInvoice $supplierInvoice)
    {
        $type = $request->query('type', 'document'); // 'document' or 'payment_proof'

        if ($type === 'payment_proof') {
            if (!$supplierInvoice->payment_proof_path || !Storage::disk('documents')->exists($supplierInvoice->payment_proof_path)) {
                return back()->with('error', 'Comprovativo não encontrado!');
            }

            $path = Storage::disk('documents')->path($supplierInvoice->payment_proof_path);
            return response()->download($path);
        }

        // Default to document
        if (!$supplierInvoice->document_path || !Storage::disk('documents')->exists($supplierInvoice->document_path)) {
            return back()->with('error', 'Documento não encontrado!');
        }

        $path = Storage::disk('documents')->path($supplierInvoice->document_path);
        return response()->download($path);
    }
}

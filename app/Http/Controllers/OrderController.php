<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Entity;
use App\Models\Article;
use App\Models\VatRate;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['entity', 'lines']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhereHas('entity', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest('order_date')->paginate(15)->withQueryString();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextNumber = Order::getNextNumber();
        $clients = Entity::clients()->active()->orderBy('name')->get();
        $articles = Article::active()->with('vatRate')->orderBy('name')->get();
        $suppliers = Entity::suppliers()->active()->orderBy('name')->get();
        $vatRates = VatRate::active()->orderBy('rate')->get();

        return Inertia::render('Orders/Create', [
            'nextNumber' => $nextNumber,
            'clients' => $clients,
            'articles' => $articles,
            'suppliers' => $suppliers,
            'vatRates' => $vatRates,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_date' => 'required|date',
            'entity_id' => 'required|exists:entities,id',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.article_id' => 'nullable|exists:articles,id',
            'lines.*.article_name' => 'required|string',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.discount' => 'nullable|numeric|min:0',
            'lines.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'lines.*.vat_rate_id' => 'required|exists:vat_rates,id',
            'lines.*.supplier_id' => 'nullable|exists:entities,id',
            'lines.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_date' => $validated['order_date'],
                'entity_id' => $validated['entity_id'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['lines'] as $index => $lineData) {
                $lineData['sort_order'] = $index;
                $order->lines()->create($lineData);
            }

            $order->calculateTotals();

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Encomenda criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao criar encomenda: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['entity.country', 'proposal', 'lines.article', 'lines.supplier']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        if ($order->isClosed()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Não é possível editar uma encomenda fechada!');
        }

        $order->load(['lines']);
        $clients = Entity::clients()->active()->orderBy('name')->get();
        $articles = Article::active()->with('vatRate')->orderBy('name')->get();
        $suppliers = Entity::suppliers()->active()->orderBy('name')->get();
        $vatRates = VatRate::active()->orderBy('rate')->get();

        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'clients' => $clients,
            'articles' => $articles,
            'suppliers' => $suppliers,
            'vatRates' => $vatRates,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        if ($order->isClosed()) {
            return back()->with('error', 'Não é possível editar uma encomenda fechada!');
        }

        $validated = $request->validate([
            'order_date' => 'required|date',
            'entity_id' => 'required|exists:entities,id',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.article_id' => 'nullable|exists:articles,id',
            'lines.*.article_name' => 'required|string',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.discount' => 'nullable|numeric|min:0',
            'lines.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'lines.*.vat_rate_id' => 'required|exists:vat_rates,id',
            'lines.*.supplier_id' => 'nullable|exists:entities,id',
            'lines.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $order->update([
                'order_date' => $validated['order_date'],
                'entity_id' => $validated['entity_id'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete existing lines
            $order->lines()->delete();

            // Create new lines
            foreach ($validated['lines'] as $index => $lineData) {
                $lineData['sort_order'] = $index;
                $order->lines()->create($lineData);
            }

            $order->calculateTotals();

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Encomenda atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao atualizar encomenda: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        if ($order->isClosed()) {
            return back()->with('error', 'Não é possível remover uma encomenda fechada!');
        }

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Encomenda removida com sucesso!');
    }

    /**
     * Close the order.
     */
    public function close(Order $order)
    {
        if ($order->isClosed()) {
            return back()->with('error', 'Encomenda já está fechada!');
        }

        try {
            $order->close();

            // Optionally create supplier orders
            if ($order->lines()->whereNotNull('supplier_id')->exists()) {
                $proposalService = new \App\Services\ProposalService();
                $supplierOrders = $proposalService->createSupplierOrders($order);

                return back()->with('success', 'Encomenda fechada com sucesso! ' . count($supplierOrders) . ' encomenda(s) de fornecedor criada(s).');
            }

            return back()->with('success', 'Encomenda fechada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate and download PDF for the order.
     */
    public function generatePdf(Order $order)
    {
        $order->load(['entity.country', 'proposal', 'lines']);
        $company = Company::with('country')->first();

        $pdf = Pdf::loadView('pdfs.order', [
            'order' => $order,
            'company' => $company,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Encomenda_' . $order->number . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Convert Order to Supplier Orders grouped by supplier.
     */
    public function convertToSupplierOrders(Order $order)
    {
        if (!$order->isClosed()) {
            return back()->with('error', 'Apenas encomendas fechadas podem ser convertidas!');
        }

        // Check if order has lines with suppliers
        if (!$order->lines()->whereNotNull('supplier_id')->exists()) {
            return back()->with('error', 'Esta encomenda não tem linhas com fornecedores definidos!');
        }

        DB::beginTransaction();
        try {
            $supplierOrders = [];

            // Group lines by supplier
            $linesBySupplier = $order->lines()
                ->whereNotNull('supplier_id')
                ->with(['article', 'vatRate'])
                ->get()
                ->groupBy('supplier_id');

            foreach ($linesBySupplier as $supplierId => $lines) {
                // Create supplier order
                $supplierOrder = \App\Models\SupplierOrder::create([
                    'number' => 'SF' . str_pad((\App\Models\SupplierOrder::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT),
                    'order_date' => now(),
                    'supplier_id' => $supplierId,
                    'order_id' => $order->id,
                    'status' => 'draft',
                ]);

                // Create supplier order lines
                foreach ($lines as $line) {
                    $supplierOrder->lines()->create([
                        'article_id' => $line->article_id,
                        'quantity' => $line->quantity,
                        'unit_price' => $line->cost_price ?? $line->unit_price,
                        'tax_rate' => $line->vatRate->rate ?? 0,
                    ]);
                }

                $supplierOrders[] = $supplierOrder;
            }

            DB::commit();

            return back()->with('success', count($supplierOrders) . ' encomenda(s) de fornecedor criada(s) com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar encomendas de fornecedor: ' . $e->getMessage());
        }
    }
}

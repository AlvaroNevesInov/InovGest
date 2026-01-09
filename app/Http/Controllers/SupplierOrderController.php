<?php

namespace App\Http\Controllers;

use App\Models\SupplierOrder;
use App\Models\Entity;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierOrder::with(['supplier', 'order']);

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

        $supplierOrders = $query->latest('order_date')->paginate(15)->withQueryString();

        return Inertia::render('SupplierOrders/Index', [
            'supplierOrders' => $supplierOrders,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function create()
    {
        $nextNumber = 'SF' . str_pad((SupplierOrder::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        $suppliers = Entity::suppliers()->active()->orderBy('name')->get();
        $articles = Article::active()->orderBy('name')->get();

        return Inertia::render('SupplierOrders/Create', [
            'nextNumber' => $nextNumber,
            'suppliers' => $suppliers,
            'articles' => $articles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_date' => 'required|date',
            'supplier_id' => 'required|exists:entities,id',
            'order_id' => 'nullable|exists:orders,id',
            'lines' => 'required|array|min:1',
            'lines.*.article_id' => 'required|exists:articles,id',
            'lines.*.quantity' => 'required|integer|min:1',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.tax_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $validated['number'] = 'SF' . str_pad((SupplierOrder::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
            $validated['status'] = 'draft';

            $lines = $validated['lines'];
            unset($validated['lines']);

            $supplierOrder = SupplierOrder::create($validated);

            foreach ($lines as $line) {
                $supplierOrder->lines()->create($line);
            }
        });

        return redirect()->route('supplier-orders.index')->with('success', 'Encomenda de fornecedor criada com sucesso!');
    }

    public function show(SupplierOrder $supplierOrder)
    {
        $supplierOrder->load(['supplier', 'order', 'lines.article']);

        return Inertia::render('SupplierOrders/Show', [
            'supplierOrder' => $supplierOrder,
        ]);
    }

    public function edit(SupplierOrder $supplierOrder)
    {
        if ($supplierOrder->status !== 'draft') {
            return redirect()->route('supplier-orders.show', $supplierOrder)
                ->with('error', 'Apenas encomendas em rascunho podem ser editadas.');
        }

        $supplierOrder->load('lines.article');
        $suppliers = Entity::suppliers()->active()->orderBy('name')->get();
        $articles = Article::active()->orderBy('name')->get();

        return Inertia::render('SupplierOrders/Edit', [
            'supplierOrder' => $supplierOrder,
            'suppliers' => $suppliers,
            'articles' => $articles,
        ]);
    }

    public function update(Request $request, SupplierOrder $supplierOrder)
    {
        if ($supplierOrder->status !== 'draft') {
            return redirect()->route('supplier-orders.show', $supplierOrder)
                ->with('error', 'Apenas encomendas em rascunho podem ser editadas.');
        }

        $validated = $request->validate([
            'order_date' => 'required|date',
            'supplier_id' => 'required|exists:entities,id',
            'lines' => 'required|array|min:1',
            'lines.*.article_id' => 'required|exists:articles,id',
            'lines.*.quantity' => 'required|integer|min:1',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.tax_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $supplierOrder) {
            $lines = $validated['lines'];
            unset($validated['lines']);

            $supplierOrder->update($validated);
            $supplierOrder->lines()->delete();

            foreach ($lines as $line) {
                $supplierOrder->lines()->create($line);
            }
        });

        return redirect()->route('supplier-orders.index')->with('success', 'Encomenda de fornecedor atualizada com sucesso!');
    }

    public function destroy(SupplierOrder $supplierOrder)
    {
        if ($supplierOrder->status !== 'draft') {
            return redirect()->route('supplier-orders.index')
                ->with('error', 'Apenas encomendas em rascunho podem ser eliminadas.');
        }

        $supplierOrder->delete();

        return redirect()->route('supplier-orders.index')->with('success', 'Encomenda de fornecedor eliminada com sucesso!');
    }

    public function send(SupplierOrder $supplierOrder)
    {
        if ($supplierOrder->status !== 'draft') {
            return back()->with('error', 'Apenas encomendas em rascunho podem ser enviadas.');
        }

        $supplierOrder->update(['status' => 'sent']);

        return back()->with('success', 'Encomenda enviada com sucesso!');
    }

    public function generatePdf(SupplierOrder $supplierOrder)
    {
        $supplierOrder->load(['supplier', 'lines.article']);

        $pdf = Pdf::loadView('pdfs.supplier-order', [
            'supplierOrder' => $supplierOrder,
        ]);

        return $pdf->download('encomenda-fornecedor-' . $supplierOrder->number . '.pdf');
    }
}

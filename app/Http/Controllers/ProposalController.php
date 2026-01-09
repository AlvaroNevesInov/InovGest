<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Entity;
use App\Models\Article;
use App\Models\VatRate;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proposal::with(['entity', 'lines']);

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

        $proposals = $query->latest('proposal_date')->paginate(15)->withQueryString();

        return Inertia::render('Proposals/Index', [
            'proposals' => $proposals,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextNumber = Proposal::getNextNumber();
        $clients = Entity::clients()->active()->orderBy('name')->get();
        $articles = Article::active()->with('vatRate')->orderBy('name')->get();
        $suppliers = Entity::suppliers()->active()->orderBy('name')->get();
        $vatRates = VatRate::active()->orderBy('rate')->get();

        return Inertia::render('Proposals/Create', [
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
            'proposal_date' => 'required|date',
            'entity_id' => 'required|exists:entities,id',
            'validity_date' => 'required|date|after:proposal_date',
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
            $proposal = Proposal::create([
                'proposal_date' => $validated['proposal_date'],
                'entity_id' => $validated['entity_id'],
                'validity_date' => $validated['validity_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['lines'] as $index => $lineData) {
                $lineData['sort_order'] = $index;
                $proposal->lines()->create($lineData);
            }

            $proposal->calculateTotals();

            DB::commit();

            return redirect()->route('proposals.show', $proposal)->with('success', 'Proposta criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao criar proposta: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Proposal $proposal)
    {
        $proposal->load(['entity.country', 'lines.article', 'lines.supplier']);

        return Inertia::render('Proposals/Show', [
            'proposal' => $proposal,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proposal $proposal)
    {
        if ($proposal->isClosed()) {
            return redirect()->route('proposals.show', $proposal)
                ->with('error', 'Não é possível editar uma proposta fechada!');
        }

        $proposal->load(['lines']);
        $clients = Entity::clients()->active()->orderBy('name')->get();
        $articles = Article::active()->with('vatRate')->orderBy('name')->get();
        $suppliers = Entity::suppliers()->active()->orderBy('name')->get();
        $vatRates = VatRate::active()->orderBy('rate')->get();

        return Inertia::render('Proposals/Edit', [
            'proposal' => $proposal,
            'clients' => $clients,
            'articles' => $articles,
            'suppliers' => $suppliers,
            'vatRates' => $vatRates,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proposal $proposal)
    {
        if ($proposal->isClosed()) {
            return back()->with('error', 'Não é possível editar uma proposta fechada!');
        }

        $validated = $request->validate([
            'proposal_date' => 'required|date',
            'entity_id' => 'required|exists:entities,id',
            'validity_date' => 'required|date|after:proposal_date',
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
            $proposal->update([
                'proposal_date' => $validated['proposal_date'],
                'entity_id' => $validated['entity_id'],
                'validity_date' => $validated['validity_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete existing lines
            $proposal->lines()->delete();

            // Create new lines
            foreach ($validated['lines'] as $index => $lineData) {
                $lineData['sort_order'] = $index;
                $proposal->lines()->create($lineData);
            }

            $proposal->calculateTotals();

            DB::commit();

            return redirect()->route('proposals.show', $proposal)->with('success', 'Proposta atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao atualizar proposta: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal)
    {
        if ($proposal->isClosed()) {
            return back()->with('error', 'Não é possível remover uma proposta fechada!');
        }

        $proposal->delete();

        return redirect()->route('proposals.index')->with('success', 'Proposta removida com sucesso!');
    }

    /**
     * Close the proposal.
     */
    public function close(Proposal $proposal)
    {
        if ($proposal->isClosed()) {
            return back()->with('error', 'Proposta já está fechada!');
        }

        $proposal->close();

        return back()->with('success', 'Proposta fechada com sucesso!');
    }

    /**
     * Convert proposal to order.
     */
    public function convertToOrder(Proposal $proposal)
    {
        try {
            $proposalService = new \App\Services\ProposalService();
            $order = $proposalService->convertToOrder($proposal);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Proposta convertida em encomenda com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate and download PDF for the proposal.
     */
    public function generatePdf(Proposal $proposal)
    {
        $proposal->load(['entity.country', 'lines']);
        $company = Company::with('country')->first();

        $pdf = Pdf::loadView('pdfs.proposal', [
            'proposal' => $proposal,
            'company' => $company,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Proposta_' . $proposal->number . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

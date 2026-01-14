<?php

namespace App\Http\Controllers;

use App\Models\VatRate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VatRateController extends Controller
{
    public function index(Request $request)
    {
        $query = VatRate::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('active') && $request->active !== 'all') {
            $query->where('active', $request->active === '1');
        }

        $vatRates = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('VatRates/Index', [
            'vatRates' => $vatRates,
            'filters' => $request->only(['search', 'active']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vat_rates,name',
            'rate' => 'required|numeric|min:0|max:100',
            'active' => 'boolean',
        ]);

        VatRate::create($validated);

        return back()->with('success', 'Taxa de IVA criada com sucesso!');
    }

    public function update(Request $request, VatRate $vatRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vat_rates,name,' . $vatRate->id,
            'rate' => 'required|numeric|min:0|max:100',
            'active' => 'boolean',
        ]);

        $vatRate->update($validated);

        return back()->with('success', 'Taxa de IVA atualizada com sucesso!');
    }

    public function destroy(VatRate $vatRate)
    {
        $vatRate->delete();

        return redirect()->route('settings.vat-rates.index')
            ->with('success', 'Taxa de IVA eliminada com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = BankAccount::query();

        if ($request->has('active')) {
            $query->where('active', $request->active);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('bank_name', 'like', "%{$search}%")
                    ->orWhere('iban', 'like', "%{$search}%");
            });
        }

        $bankAccounts = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('BankAccounts/Index', [
            'bankAccounts' => $bankAccounts,
            'filters' => $request->only(['active', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:255|unique:bank_accounts,iban',
            'swift_bic' => 'nullable|string|max:255',
            'currency' => 'required|string|size:3',
            'initial_balance' => 'required|numeric',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $validated['current_balance'] = $validated['initial_balance'];

        BankAccount::create($validated);

        return back()->with('success', 'Conta bancária criada com sucesso!');
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:255|unique:bank_accounts,iban,' . $bankAccount->id,
            'swift_bic' => 'nullable|string|max:255',
            'currency' => 'required|string|size:3',
            'current_balance' => 'required|numeric',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $bankAccount->update($validated);

        return back()->with('success', 'Conta bancária atualizada com sucesso!');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')->with('success', 'Conta bancária eliminada com sucesso!');
    }
}

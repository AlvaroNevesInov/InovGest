<?php

namespace App\Http\Controllers;

use App\Models\ClientAccount;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ClientAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientAccount::with('entity');

        if ($request->has('entity_id')) {
            $query->where('entity_id', $request->entity_id);
        }

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhereHas('entity', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $movements = $query->latest('movement_date')->paginate(15)->withQueryString();
        $clients = Entity::clients()->active()->orderBy('name')->get();

        return Inertia::render('ClientAccounts/Index', [
            'movements' => $movements,
            'clients' => $clients,
            'filters' => $request->only(['entity_id', 'type', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'movement_date' => 'required|date',
            'type' => 'required|in:debit,credit',
            'document_type' => 'nullable|string|max:255',
            'document_id' => 'nullable|integer',
            'document_number' => 'nullable|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            // Get last balance for this client
            $lastMovement = ClientAccount::where('entity_id', $validated['entity_id'])
                ->latest('movement_date')
                ->latest('id')
                ->first();

            $previousBalance = $lastMovement ? $lastMovement->balance : 0;

            // Calculate new balance
            if ($validated['type'] === 'debit') {
                $validated['balance'] = $previousBalance + $validated['amount'];
            } else {
                $validated['balance'] = $previousBalance - $validated['amount'];
            }

            ClientAccount::create($validated);
        });

        return back()->with('success', 'Movimento criado com sucesso!');
    }

    public function update(Request $request, ClientAccount $clientAccount)
    {
        $validated = $request->validate([
            'movement_date' => 'required|date',
            'type' => 'required|in:debit,credit',
            'document_type' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $clientAccount) {
            $clientAccount->update($validated);

            // Recalculate balances for all subsequent movements
            $this->recalculateBalances($clientAccount->entity_id);
        });

        return back()->with('success', 'Movimento atualizado com sucesso!');
    }

    public function destroy(ClientAccount $clientAccount)
    {
        $entityId = $clientAccount->entity_id;

        $clientAccount->delete();

        // Recalculate balances
        $this->recalculateBalances($entityId);

        return back()->with('success', 'Movimento eliminado com sucesso!');
    }

    /**
     * Recalculate balances for all movements of an entity.
     */
    private function recalculateBalances(int $entityId): void
    {
        $movements = ClientAccount::where('entity_id', $entityId)
            ->orderBy('movement_date')
            ->orderBy('id')
            ->get();

        $balance = 0;
        foreach ($movements as $movement) {
            if ($movement->type === 'debit') {
                $balance += $movement->amount;
            } else {
                $balance -= $movement->amount;
            }
            $movement->update(['balance' => $balance]);
        }
    }
}

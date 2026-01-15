<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Tenants/Index', [
            'tenants' => $user->tenants()
                ->withPivot('role')
                ->orderBy('name')
                ->get(),
            'currentTenant' => $user->currentTenant(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenants/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('tenants', 'slug')],
        ]);

        /** @var User $user */
        $user = Auth::user();

        DB::beginTransaction();

        try {
            // Criar o tenant
            $tenant = Tenant::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?? Str::slug($validated['name']),
                'owner_id' => $user->id,
                'active' => true,
            ]);

            // Adicionar o usuário ao tenant com role "owner"
            $tenant->users()->attach($user->id, ['role' => 'owner']);

            // Definir como tenant atual
            session(['current_tenant_id' => $tenant->id]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Tenant criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao criar tenant: ' . $e->getMessage()]);
        }
    }

    public function switch(Request $request, Tenant $tenant)
    {
        /** @var User $user */
        $user = Auth::user();

        // Verificar se o usuário tem acesso ao tenant
        if (!$user->tenants()->where('tenants.id', $tenant->id)->exists()) {
            return back()->withErrors(['error' => 'Você não tem acesso a este tenant.']);
        }

        // Trocar o tenant
        $user->switchTenant($tenant);

        return back()->with('success', 'Tenant alterado com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'companies']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();

        // Get all companies that the current user has access to
        $currentUser = $request->user();
        $companies = $currentUser->companies()->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'companies' => $companies,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'companies' => 'nullable|array',
            'companies.*' => 'exists:companies,id',
            'company_owners' => 'nullable|array',
            'company_owners.*' => 'exists:companies,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        // Attach companies to user
        if (isset($validated['companies']) && is_array($validated['companies'])) {
            $companyOwners = $validated['company_owners'] ?? [];

            foreach ($validated['companies'] as $companyId) {
                $isOwner = in_array($companyId, $companyOwners);
                $user->companies()->attach($companyId, ['is_owner' => $isOwner]);
            }

            // Set first company as current if user has companies
            if (count($validated['companies']) > 0) {
                $user->current_company_id = $validated['companies'][0];
                $user->save();
            }
        }

        return back()->with('success', 'Utilizador criado com sucesso!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'companies' => 'nullable|array',
            'companies.*' => 'exists:companies,id',
            'company_owners' => 'nullable|array',
            'company_owners.*' => 'exists:companies,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (isset($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        // Sync companies with owner flag
        if (isset($validated['companies'])) {
            $companyOwners = $validated['company_owners'] ?? [];

            // Detach all companies first
            $user->companies()->detach();

            // Attach selected companies with owner flag
            foreach ($validated['companies'] as $companyId) {
                $isOwner = in_array($companyId, $companyOwners);
                $user->companies()->attach($companyId, ['is_owner' => $isOwner]);
            }

            // If current company was removed, set to first available or null
            if (!in_array($user->current_company_id, $validated['companies'])) {
                $user->current_company_id = count($validated['companies']) > 0 ? $validated['companies'][0] : null;
                $user->save();
            }
        }

        return back()->with('success', 'Utilizador atualizado com sucesso!');
    }

    public function destroy(User $user)
{
    if ($user->id === Auth::id()) {
        return back()->with('error', 'Não pode eliminar o seu próprio utilizador!');
    }

    $user->delete();

    return redirect()->route('users.index')
        ->with('success', 'Utilizador eliminado com sucesso!');
}
}

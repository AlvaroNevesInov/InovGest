<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // Get all companies the user has access to
        $companies = $user->companies()->with('country')->get();
        $countries = Country::active()->orderBy('name')->get();

        return Inertia::render('Settings/Companies/Index', [
            'companies' => $companies,
            'countries' => $countries,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nif' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        // Create the company
        $company = Company::create($validated);

        // Attach the current user as owner of the new company
        $request->user()->companies()->attach($company->id, ['is_owner' => true]);

        // Set as current company if user doesn't have one
        if (!$request->user()->current_company_id) {
            $request->user()->current_company_id = $company->id;
            $request->user()->save();
        }

        return back()->with('success', 'Dados da empresa criados com sucesso!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        // Only owners can update companies
        if (!$request->user()->isOwnerOf($company->id)) {
            abort(403, 'Apenas proprietários podem editar esta empresa.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nif' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($validated);

        return back()->with('success', 'Dados da empresa atualizados com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Company $company)
    {
        // Only owners can delete companies
        if (!$request->user()->isOwnerOf($company->id)) {
            abort(403, 'Apenas proprietários podem remover esta empresa.');
        }

        // Prevent deleting if it's the user's current company
        if ($request->user()->current_company_id === $company->id) {
            return back()->withErrors(['error' => 'Não pode remover a empresa atualmente ativa. Mude para outra empresa primeiro.']);
        }

        if ($company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
        }

        $company->delete();

        return redirect()->route('settings.companies.index')->with('success', 'Empresa removida com sucesso!');
    }
}

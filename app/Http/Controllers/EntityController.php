<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Country;
use App\Services\ViesService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Entity::with('country');

        if ($request->has('type') && $request->type !== 'all') {
            if ($request->type === 'client') {
                $query->clients();
            } elseif ($request->type === 'supplier') {
                $query->suppliers();
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nif', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('active')) {
            $query->where('active', $request->active);
        }

        $entities = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Entities/Index', [
            'entities' => $entities,
            'filters' => $request->only(['type', 'search', 'active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextNumber = Entity::max('number') + 1;
        $countries = Country::active()->orderBy('name')->get();

        return Inertia::render('Entities/Create', [
            'nextNumber' => $nextNumber,
            'countries' => $countries,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:client,supplier,both',
            'nif' => ['required', 'string', 'regex:/^([A-Z]{2}[0-9A-Z]{8,12}|[0-9]{9})$/', 'unique:entities,nif'],
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => ['nullable', 'string', 'regex:/^\d{4}-\d{3}$/'],
            'city' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'rgpd_consent' => 'boolean',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ], [
            'nif.regex' => 'O NIF deve ter 9 dígitos (ex: 123456789) ou formato europeu (ex: PT123456789).',
        ]);

        $validated['number'] = Entity::max('number') + 1;

        Entity::create($validated);

        return redirect()->route('entities.index')->with('success', 'Entidade criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Entity $entity)
    {
        $entity->load('country');

        return Inertia::render('Entities/Show', [
            'entity' => $entity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entity $entity)
    {
        $countries = Country::active()->orderBy('name')->get();

        return Inertia::render('Entities/Edit', [
            'entity' => $entity,
            'countries' => $countries,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'type' => 'required|in:client,supplier,both',
            'nif' => ['required', 'string', 'regex:/^([A-Z]{2}[0-9A-Z]{8,12}|[0-9]{9})$/', 'unique:entities,nif,' . $entity->id],
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => ['nullable', 'string', 'regex:/^\d{4}-\d{3}$/'],
            'city' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'rgpd_consent' => 'boolean',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ], [
            'nif.regex' => 'O NIF deve ter 9 dígitos (ex: 123456789) ou formato europeu (ex: PT123456789).',
        ]);

        $entity->update($validated);

        return redirect()->route('entities.index')->with('success', 'Entidade atualizada com sucesso!');
    }

    /**
     * Validate NIF with VIES and get company data.
     */
    public function validateVies(Request $request, ViesService $viesService)
    {
        $request->validate([
            'nif' => 'required|string',
        ]);

        $extracted = $viesService->extractCountryCode($request->nif);
        $result = $viesService->validate($extracted['country_code'], $extracted['vat_number']);

        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        $entity->delete();

        return redirect()->route('entities.index')->with('success', 'Entidade removida com sucesso!');
    }
}

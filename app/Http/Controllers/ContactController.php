<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Entity;
use App\Models\ContactFunction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contact::with(['entity', 'contactFunction']);

        // Filter by entity
        if ($request->has('entity_id') && $request->entity_id) {
            $query->where('entity_id', $request->entity_id);
        }

        // Filter by active/inactive
        if ($request->has('status')) {
            $query->where('active', $request->status === 'active');
        }

        // Search by name, surname, email, or phone
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $contacts = $query->orderBy('number', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'filters' => $request->only(['entity_id', 'status', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $nextNumber = Contact::max('number') + 1;

        $entities = Entity::active()
                         ->orderBy('name')
                         ->get(['id', 'name', 'type']);

        $contactFunctions = ContactFunction::active()
                                          ->orderBy('name')
                                          ->get(['id', 'name']);

        return Inertia::render('Contacts/Create', [
            'nextNumber' => $nextNumber,
            'entities' => $entities,
            'contactFunctions' => $contactFunctions,
            'entityId' => $request->get('entity_id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'contact_function_id' => 'nullable|exists:contact_functions,id',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'rgpd_consent' => 'boolean',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        // Generate next number
        $validated['number'] = Contact::max('number') + 1;

        Contact::create($validated);

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Contacto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $contact->load(['entity', 'contactFunction']);

        return Inertia::render('Contacts/Show', [
            'contact' => $contact,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        $contact->load(['entity', 'contactFunction']);

        $entities = Entity::active()
                         ->orderBy('name')
                         ->get(['id', 'name', 'type']);

        $contactFunctions = ContactFunction::active()
                                          ->orderBy('name')
                                          ->get(['id', 'name']);

        return Inertia::render('Contacts/Edit', [
            'contact' => $contact,
            'entities' => $entities,
            'contactFunctions' => $contactFunctions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'contact_function_id' => 'nullable|exists:contact_functions,id',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'rgpd_consent' => 'boolean',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $contact->update($validated);

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Contacto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Contacto eliminado com sucesso!');
    }
}

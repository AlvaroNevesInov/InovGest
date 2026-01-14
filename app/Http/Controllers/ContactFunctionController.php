<?php

namespace App\Http\Controllers;

use App\Models\ContactFunction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactFunctionController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactFunction::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('active') && $request->active !== 'all') {
            $query->where('active', $request->active === '1');
        }

        $contactFunctions = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('ContactFunctions/Index', [
            'contactFunctions' => $contactFunctions,
            'filters' => $request->only(['search', 'active']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contact_functions,name',
            'active' => 'boolean',
        ]);

        ContactFunction::create($validated);

        return back()->with('success', 'Função de contacto criada com sucesso!');
    }

    public function update(Request $request, ContactFunction $contactFunction)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contact_functions,name,' . $contactFunction->id,
            'active' => 'boolean',
        ]);

        $contactFunction->update($validated);

        return back()->with('success', 'Função de contacto atualizada com sucesso!');
    }

    public function destroy(ContactFunction $contactFunction)
    {
        $contactFunction->delete();

        return redirect()->route('contact-functions.index')
            ->with('success', 'Função de contacto eliminada com sucesso!');
    }
}

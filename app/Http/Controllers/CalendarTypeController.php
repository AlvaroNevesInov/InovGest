<?php

namespace App\Http\Controllers;

use App\Models\CalendarType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = CalendarType::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('active') && $request->active !== 'all') {
            $query->where('active', $request->active === '1');
        }

        $calendarTypes = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('CalendarTypes/Index', [
            'calendarTypes' => $calendarTypes,
            'filters' => $request->only(['search', 'active']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calendar_types,name',
            'active' => 'boolean',
        ]);

        CalendarType::create($validated);

        return back()->with('success', 'Tipo de calendário criado com sucesso!');
    }

    public function update(Request $request, CalendarType $calendarType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calendar_types,name,' . $calendarType->id,
            'active' => 'boolean',
        ]);

        $calendarType->update($validated);

        return back()->with('success', 'Tipo de calendário atualizado com sucesso!');
    }

    public function destroy(CalendarType $calendarType)
    {
        $calendarType->delete();

        return redirect()->route('calendar-types.index')
            ->with('success', 'Tipo de calendário eliminado com sucesso!');
    }
}

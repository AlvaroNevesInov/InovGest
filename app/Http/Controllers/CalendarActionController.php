<?php

namespace App\Http\Controllers;

use App\Models\CalendarAction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarActionController extends Controller
{
    public function index(Request $request)
    {
        $query = CalendarAction::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('active') && $request->active !== 'all') {
            $query->where('active', $request->active === '1');
        }

        $calendarActions = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('CalendarActions/Index', [
            'calendarActions' => $calendarActions,
            'filters' => $request->only(['search', 'active']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calendar_actions,name',
            'active' => 'boolean',
        ]);

        CalendarAction::create($validated);

        return back()->with('success', 'Ação de calendário criada com sucesso!');
    }

    public function update(Request $request, CalendarAction $calendarAction)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calendar_actions,name,' . $calendarAction->id,
            'active' => 'boolean',
        ]);

        $calendarAction->update($validated);

        return back()->with('success', 'Ação de calendário atualizada com sucesso!');
    }

    public function destroy(CalendarAction $calendarAction)
    {
        $calendarAction->delete();

        return redirect()->route('calendar-actions.index')
            ->with('success', 'Ação de calendário eliminada com sucesso!');
    }
}

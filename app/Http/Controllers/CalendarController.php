<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\CalendarType;
use App\Models\CalendarAction;
use App\Models\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $query = Calendar::with(['calendarType', 'calendarAction', 'entity', 'user']);

        // Filter by date range
        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('start_date', [$request->start, $request->end]);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by type
        if ($request->has('type_id')) {
            $query->where('calendar_type_id', $request->type_id);
        }

        $events = $query->orderBy('start_date')->get();
        $types = CalendarType::active()->orderBy('name')->get();
        $actions = CalendarAction::active()->orderBy('name')->get();
        $entities = Entity::active()->orderBy('name')->limit(100)->get();

        return Inertia::render('Calendar/Index', [
            'events' => $events,
            'types' => $types,
            'actions' => $actions,
            'entities' => $entities,
            'filters' => $request->only(['start', 'end', 'user_id', 'type_id']),
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'all_day' => 'boolean',
        'calendar_type_id' => 'required|exists:calendar_types,id',
        'calendar_action_id' => 'required|exists:calendar_actions,id',
        'entity_id' => 'nullable|exists:entities,id',
        'user_id' => 'nullable|exists:users,id',
        'color' => 'nullable|string|max:7',
        'notes' => 'nullable|string',
    ]);

    $validated['user_id'] = $validated['user_id'] ?? Auth::id();

    Calendar::create($validated);

    return back()->with('success', 'Evento criado com sucesso!');
}


    public function update(Request $request, Calendar $calendar)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'all_day' => 'boolean',
            'calendar_type_id' => 'required|exists:calendar_types,id',
            'calendar_action_id' => 'required|exists:calendar_actions,id',
            'entity_id' => 'nullable|exists:entities,id',
            'color' => 'nullable|string|max:7',
            'notes' => 'nullable|string',
        ]);

        $calendar->update($validated);

        return back()->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Calendar $calendar)
    {
        $calendar->delete();

        return back()->with('success', 'Evento eliminado com sucesso!');
    }
}

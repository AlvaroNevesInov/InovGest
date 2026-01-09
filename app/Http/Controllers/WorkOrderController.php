<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Entity;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkOrder::with(['entity', 'order']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('entity', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $workOrders = $query->latest('work_date')->paginate(15)->withQueryString();

        return Inertia::render('WorkOrders/Index', [
            'workOrders' => $workOrders,
            'filters' => $request->only(['status', 'priority', 'search']),
        ]);
    }

    public function create()
    {
        $nextNumber = 'OT' . str_pad((WorkOrder::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        $entities = Entity::clients()->active()->orderBy('name')->get();
        $orders = Order::with('entity')->latest()->take(50)->get();

        return Inertia::render('WorkOrders/Create', [
            'nextNumber' => $nextNumber,
            'entities' => $entities,
            'orders' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_date' => 'required|date',
            'entity_id' => 'required|exists:entities,id',
            'order_id' => 'nullable|exists:orders,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,normal,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $validated['number'] = 'OT' . str_pad((WorkOrder::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        WorkOrder::create($validated);

        return redirect()->route('work-orders.index')->with('success', 'Ordem de trabalho criada com sucesso!');
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['entity', 'order']);

        return Inertia::render('WorkOrders/Show', [
            'workOrder' => $workOrder,
        ]);
    }

    public function edit(WorkOrder $workOrder)
    {
        $entities = Entity::clients()->active()->orderBy('name')->get();
        $orders = Order::with('entity')->latest()->take(50)->get();

        return Inertia::render('WorkOrders/Edit', [
            'workOrder' => $workOrder,
            'entities' => $entities,
            'orders' => $orders,
        ]);
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'work_date' => 'required|date',
            'entity_id' => 'required|exists:entities,id',
            'order_id' => 'nullable|exists:orders,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,normal,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'completed_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $workOrder->update($validated);

        return redirect()->route('work-orders.index')->with('success', 'Ordem de trabalho atualizada com sucesso!');
    }

    public function destroy(WorkOrder $workOrder)
    {
        $workOrder->delete();

        return redirect()->route('work-orders.index')->with('success', 'Ordem de trabalho eliminada com sucesso!');
    }
}

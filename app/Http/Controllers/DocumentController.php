<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Display a listing of all documents (proposals and orders).
     */
    public function index(Request $request)
    {
        $proposalsQuery = \App\Models\Proposal::with('entity');
        $ordersQuery = \App\Models\Order::with('entity');

        // Search by number or client name
        if ($request->has('search')) {
            $search = $request->search;

            $proposalsQuery->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhereHas('entity', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });

            $ordersQuery->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhereHas('entity', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by type if provided
        $type = $request->get('type', 'all');

        $proposals = $type === 'orders' ? collect([]) : $proposalsQuery->latest('proposal_date')->get();
        $orders = $type === 'proposals' ? collect([]) : $ordersQuery->latest('order_date')->get();

        return inertia('Documents/Index', [
            'proposals' => $proposals,
            'orders' => $orders,
            'filters' => $request->only(['search', 'type']),
        ]);
    }
}

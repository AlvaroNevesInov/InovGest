<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PlanController extends Controller
{
    /**
     * Display all available plans
     */
    public function index()
    {
        $tenantId = session('current_tenant_id');
        $currentSubscription = null;
        $usageSummary = null;

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if ($tenant && $tenant->hasActiveSubscription()) {
                $currentSubscription = $tenant->activeSubscription()->with('plan')->first();

                // Get usage summary
                $usageSummary = $currentSubscription->usage()
                    ->get()
                    ->map(function ($usage) {
                        return [
                            'feature' => $usage->feature,
                            'used' => $usage->used,
                            'limit' => $usage->limit,
                            'remaining' => $usage->getRemaining(),
                            'percentage' => $usage->getPercentage(),
                            'has_reached_limit' => $usage->hasReachedLimit(),
                        ];
                    });
            }
        }

        $plans = Plan::active()
            ->ordered()
            ->get();

        return Inertia::render('Subscriptions/Plans', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
            'usageSummary' => $usageSummary,
        ]);
    }

    /**
     * Display a specific plan
     */
    public function show(Plan $plan)
    {
        $tenantId = session('current_tenant_id');
        $currentSubscription = null;

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if ($tenant && $tenant->hasActiveSubscription()) {
                $currentSubscription = $tenant->activeSubscription;
            }
        }

        return Inertia::render('Subscriptions/PlanDetail', [
            'plan' => $plan,
            'currentSubscription' => $currentSubscription,
        ]);
    }
}

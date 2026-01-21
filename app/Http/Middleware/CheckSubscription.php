<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * Check if tenant has an active subscription
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return redirect()->route('tenants.create')
                ->with('error', 'Por favor, selecione ou crie um tenant.');
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return redirect()->route('tenants.create')
                ->with('error', 'Tenant não encontrado.');
        }

        // Check if tenant has active subscription
        if (!$tenant->hasActiveSubscription()) {
            return redirect()->route('subscriptions.plans')
                ->with('warning', 'Por favor, escolha um plano para continuar.');
        }

        $subscription = $tenant->activeSubscription;

        // Check if subscription has ended
        if ($subscription->hasEnded()) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'A sua subscrição expirou. Por favor, escolha um novo plano.');
        }

        // Share subscription with all views
        view()->share('currentSubscription', $subscription);

        return $next($request);
    }
}

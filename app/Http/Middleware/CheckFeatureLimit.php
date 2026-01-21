<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureLimit
{
    /**
     * Handle an incoming request.
     *
     * Check if tenant has reached the limit for a specific feature
     *
     * Usage: Route::middleware('feature.limit:users')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return $this->limitReachedResponse($request, 'Tenant não encontrado.');
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant || !$tenant->hasActiveSubscription()) {
            return $this->limitReachedResponse($request, 'Subscrição não encontrada.');
        }

        $subscription = $tenant->activeSubscription;

        // Check if feature limit has been reached
        if ($subscription->hasReachedLimit($feature)) {
            $featureName = $this->getFeatureName($feature);
            $message = "Atingiu o limite de {$featureName} do seu plano. Por favor, faça upgrade.";

            return $this->limitReachedResponse($request, $message);
        }

        return $next($request);
    }

    /**
     * Return appropriate response when limit is reached
     */
    private function limitReachedResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'upgrade_url' => route('subscriptions.plans'),
            ], 403);
        }

        return redirect()->back()
            ->with('error', $message)
            ->with('upgrade_available', true);
    }

    /**
     * Get human-readable feature name
     */
    private function getFeatureName(string $feature): string
    {
        $names = [
            'users' => 'utilizadores',
            'companies' => 'empresas',
            'storage_gb' => 'armazenamento',
            'invoices_per_month' => 'faturas por mês',
            'proposals_per_month' => 'propostas por mês',
        ];

        return $names[$feature] ?? $feature;
    }
}

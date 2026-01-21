<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    /**
     * Handle an incoming request.
     *
     * Check if tenant's plan includes access to a specific feature
     *
     * Usage: Route::middleware('feature.access:advanced_reports')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenantId = session('current_tenant_id');

        if (!$tenantId) {
            return $this->accessDeniedResponse($request, 'Tenant não encontrado.');
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant || !$tenant->hasActiveSubscription()) {
            return $this->accessDeniedResponse($request, 'Subscrição não encontrada.');
        }

        // Check if tenant can access this feature
        if (!$tenant->canAccessFeature($feature)) {
            $featureName = $this->getFeatureName($feature);
            $message = "A funcionalidade '{$featureName}' não está disponível no seu plano. Faça upgrade para ter acesso.";

            return $this->accessDeniedResponse($request, $message);
        }

        return $next($request);
    }

    /**
     * Return appropriate response when access is denied
     */
    private function accessDeniedResponse(Request $request, string $message): Response
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
            'calendar' => 'Calendário',
            'contacts' => 'Contactos',
            'articles' => 'Artigos',
            'invoicing' => 'Faturação Avançada',
            'proposals' => 'Propostas',
            'work_orders' => 'Ordens de Trabalho',
            'advanced_reports' => 'Relatórios Avançados',
            'priority_support' => 'Suporte Prioritário',
            'custom_branding' => 'Branding Personalizado',
            'api_access' => 'Acesso à API',
            'advanced_permissions' => 'Permissões Avançadas',
        ];

        return $names[$feature] ?? $feature;
    }
}

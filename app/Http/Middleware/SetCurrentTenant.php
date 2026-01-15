<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            // Se não houver um tenant atual na sessão, definir o primeiro
            if (!session()->has('current_tenant_id')) {
                $firstTenant = $user->tenants()->first();

                if ($firstTenant) {
                    session(['current_tenant_id' => $firstTenant->id]);
                }
            }

            // Verificar se o tenant na sessão ainda existe e o usuário tem acesso
            $currentTenantId = session('current_tenant_id');
            if ($currentTenantId) {
                $hasAccess = $user->tenants()
                    ->where('tenants.id', $currentTenantId)
                    ->exists();

                if (!$hasAccess) {
                    // Se não tem acesso, remover da sessão e definir o primeiro disponível
                    session()->forget('current_tenant_id');

                    $firstTenant = $user->tenants()->first();
                    if ($firstTenant) {
                        session(['current_tenant_id' => $firstTenant->id]);
                    }
                }
            }
        }

        return $next($request);
    }
}

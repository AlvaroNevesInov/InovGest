<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // If user doesn't have a current company set, use their first company
            if (!$user->current_company_id) {
                $firstCompany = $user->companies()->first();
                if ($firstCompany) {
                    $user->current_company_id = $firstCompany->id;
                    $user->save();
                }
            }

            // Set the current company in the request for easy access
            if ($user->current_company_id) {
                $request->attributes->set('current_company_id', $user->current_company_id);

                // Store in app container for global access
                app()->instance('current_company_id', $user->current_company_id);
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Filament\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Allow access if user has one of these roles
        if ($user && $user->hasAnyRole(['admin', 'super admin', 'participant'])) {
            return $next($request);
        }

        // Abort with 403 if user doesn't have required role
        abort(403, 'You do not have permission to access the dashboard.');
    }
}

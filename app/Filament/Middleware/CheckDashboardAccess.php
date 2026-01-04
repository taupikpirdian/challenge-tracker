<?php

namespace App\Filament\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckDashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Use web guard explicitly to get authenticated user
        $user = auth('web')->user();
        $isAuthenticated = auth('web')->check();

        // Log incoming request
        Log::info('Dashboard access attempt', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'user_id' => auth('web')->id(),
            'authenticated' => $isAuthenticated,
            'session_id' => session()->getId(),
        ]);

        // Allow access if user is authenticated
        if (!$user) {
            Log::warning('Dashboard access denied: User not authenticated', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'session_has_user_id' => session()->has('user_id'),
                'session_all' => session()->all(),
            ]);
            abort(403, 'You must be logged in to access the dashboard.');
        }

        // Log user details
        $userRoles = $user->roles->pluck('name')->toArray();
        Log::info('User authentication details', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $userRoles,
            'has_any_role' => $user->hasAnyRole(['admin', 'super admin', 'participant']),
        ]);

        // Allow access if user has one of these roles
        if ($user->hasAnyRole(['admin', 'super admin', 'participant'])) {
            Log::info('Dashboard access granted', [
                'user_id' => $user->id,
                'email' => $user->email,
                'roles' => $userRoles,
            ]);
            return $next($request);
        }

        // Debug: Log the access attempt
        Log::warning('Dashboard access denied: Invalid role', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $userRoles,
            'required_roles' => ['admin', 'super admin', 'participant'],
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
        ]);

        // Abort with 403 if user doesn't have required role
        abort(403, 'You do not have permission to access the dashboard. Required roles: admin, super admin, or participant.');
    }
}

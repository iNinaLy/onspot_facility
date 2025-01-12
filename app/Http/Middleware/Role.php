<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            // Log unauthorized access attempts
            Log::warning('Unauthorized access attempt to a role-specific route.');

            // Redirect to a role-specific login page
            $loginRoute = $role === 'admin' ? 'admin.login' : ($role === 'supervisor' ? 'supervisor.login' : 'login');
            return redirect()->route($loginRoute)->with('alert', 'Please login to access this page.');
        }

        $user = $request->user();

        // Log user details and role for debugging
        Log::info('User ID: ' . $user->id . ', Role: ' . $user->role . ', Expected Role: ' . $role);

        // Check if the authenticated user's role matches the required role
        if ($user->role !== $role) {
            // Log access denial for role mismatch
            Log::warning('Access denied for User ID: ' . $user->id . ' due to role mismatch. Required: ' . $role . ', User Role: ' . $user->role);

            // Redirect to a general dashboard or an unauthorized page
            return redirect()->route('dashboard')->with('alert', 'You do not have permission to access this page.');
        }

        // Allow the request to proceed
        return $next($request);
    }
}
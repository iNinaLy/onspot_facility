<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import the Log facade

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            // Redirect to role-specific login pages
            $loginRoute = $role === 'admin' ? 'admin.login' : 'supervisor.login';
            return redirect()->route($loginRoute)->with('alert', 'Please login first.');
        }

        // Log the user's role for debugging
        Log::info('User role: ' . $request->user()->role);

        // Check if the user's role matches the expected role
        if ($request->user()->role !== $role) {
            return redirect()->route('dashboard')->with('alert', 'Access denied for this role.');
        }

        return $next($request); // Proceed to the next request
    }
}
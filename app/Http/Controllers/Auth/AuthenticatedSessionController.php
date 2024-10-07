<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate the user
        $request->authenticate();
    
        // Regenerate the session to prevent session fixation
        $request->session()->regenerate();
    
        // Set default URL
        $url = "dashboard";
    
        // Redirect based on user role
        if ($request->user()->role === "admin") {
            $url = "admin/dashboard"; // Redirect for admin
        } elseif ($request->user()->role === "supervisor") { // Check for supervisor
            $url = "supervisor/dashboard"; // Redirect for supervisor
        }
    
        // Redirect to the intended URL based on the user's role
        return redirect()->intended($url);
    }
    

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

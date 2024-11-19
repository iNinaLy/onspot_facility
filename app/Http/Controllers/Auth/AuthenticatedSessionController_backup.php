<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if ($user && $user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user && $user->role === 'supervisor') {
                return redirect()->route('supervisor.dashboard');
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
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

    /**
     * Show the admin login form.
     */
    public function showAdminLoginForm(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Handle admin login.
     */
    public function adminLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show the supervisor login form.
     */
    public function showSupervisorLoginForm(): View
    {
        return view('auth.supervisor-login');
    }

    /**
     * Handle supervisor login.
     */
    public function supervisorLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('supervisor')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('supervisor.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle admin logout.
     */
    public function adminLogout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    /**
     * Handle supervisor logout.
     */
    public function supervisorLogout(Request $request): RedirectResponse
    {
        Auth::guard('supervisor')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/supervisor/login');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return view('admin.profile.edit', ['user' => $user]);
        }

        if ($user->hasRole('supervisor')) {
            return view('supervisor.profile.edit', ['user' => $user]);
        }

        // Fallback for other roles
        abort(404, 'Profile page not found for your role.');
    }


    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Redirect back to different profile edit pages depending on role
        if ($user->hasRole('admin')) {
            return Redirect::route('admin.profile.edit')->with('status', 'profile-updated');
        }

        if ($user->hasRole('supervisor')) {
            return Redirect::route('supervisor.profile.edit')->with('status', 'profile-updated');
        }

        // Default redirect if not admin or supervisor
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

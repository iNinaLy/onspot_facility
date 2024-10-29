<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Check user role and load the respective view
        if ($user->hasRole('admin')) {
            return view('admin.profile.edit', ['user' => $user]);
        } elseif ($user->hasRole('supervisor')) {
            return view('supervisor.profile.edit', ['user' => $user]);
        }

        return view('profile.edit', ['user' => $user]);
    }

    /**
     * Return the logged-in user's profile information as JSON.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json($user);
    }

    /**
     * Handle profile picture upload.
     */
    public function storeProfilePicture(Request $request): ?string
    {
        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            return $path; // Return the relative path for database storage
        }
        return null; // Return null if no file was uploaded
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Handle profile picture upload
        $profilePicPath = $this->storeProfilePicture($request);
        if ($profilePicPath) {
            $user->profile_pic = $profilePicPath; // Save the profile picture path
        }

        $user->fill($request->validated());
        $user->save();

        // Return updated user info including profile picture
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'profile_pic' => $user->profile_pic ? asset('storage/' . $user->profile_pic) : null,
                'role' => $user->roles->pluck('name')->first(), // Retrieve role name
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]
        ], 200);
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

    /**
     * Get profile information as JSON.
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            return response()->json([
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'profile_pic' => $user->profile_pic ? asset('storage/' . $user->profile_pic) : null,
            ]);
        }

        return response()->json(['message' => 'User not found'], 404);
    }
}

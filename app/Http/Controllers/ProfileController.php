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
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Check if the user is an admin and use the admin-specific view
        if ($user->role === 'admin') {
            return view('admin.profile.edit', ['user' => $user]);
        }

        // Check if the user is a supervisor and use the supervisor-specific view
        if ($user->role === 'supervisor') {
            return view('supervisor.profile.edit', ['user' => $user]);
        }

        // Default view for other roles (or return a 403 error if not authorized)
        return abort(403, 'Unauthorized action.');
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
     * Handle profile picture storage.
     */
    public function storeProfilePicture(Request $request)
    {
        if ($request->hasFile('profile_pic')) {
            Log::info('Incoming request data:', $request->all());

            // Store the file in the 'public/profile_pics' directory
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            Log::info('Stored file path:', [$path]);

            // Return the public URL for database storage
            return $path;
        }
        return null;
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
     * API function to get profile details in JSON format.
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

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        Log::info('Profile update request received:', $request->all());
        $user = $request->user();

        if (!$user) {
            return redirect()->route('profile.edit')->with('error', 'User not authenticated.');
        }

        Log::info('Validated data:', $request->validated());

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            // Delete previous profile picture if exists
            if ($user->profile_pic) {
                Storage::disk('public')->delete($user->profile_pic);
            }

            // Store the new profile picture
            $path = $this->storeProfilePicture($request);
            $user->profile_pic = $path;
        }

        // Update user with validated data
        $user->fill($request->validated());
        $user->save();

        Log::info('User after save:', [$user->toArray()]);

        // Redirect back with a success message
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }


    public function apiupdate(ProfileUpdateRequest $request): JsonResponse
    {
        \Log::info('Profile update request received:', $request->all());
        $user = $request->user();
    
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    
        \Log::info('Validated data:', $request->validated());
    
        // Handle profile picture upload using the store method
        $profilePicPath = $this->storeProfilePicture($request);
        if ($profilePicPath) {
            $user->profile_pic = $profilePicPath; // Save the correct relative path to the user
        }
    
        $user->fill($request->validated());
        $user->save();
        \Log::info('User after save:', [$user->toArray()]);
    
        // Return updated user info including profile picture
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'profile_pic' => $user->profile_pic ? asset('storage/' . $user->profile_pic) : null, // Return full URL of profile picture
                'role' => $user->role,
                'building' => $user->building,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]
        ], 200);
    }
    

}
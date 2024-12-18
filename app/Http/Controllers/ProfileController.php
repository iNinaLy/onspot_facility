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
    public function storeProfilePicture(Request $request): JsonResponse
    {
        $user = $request->user();
    
        // Validate the uploaded file
        $request->validate([
            'profile_pic' => 'required|image|max:2048', // Max size 2MB
        ]);
    
        // Remove the existing profile picture
        $user->clearMediaCollection('profile_pictures');
    
        // Add the new profile picture
        $media = $user->addMediaFromRequest('profile_pic')
                      ->toMediaCollection('profile_pictures');
    
        // Save the URL to the 'profile_pic' column in the users table
        $user->update([
            'profile_pic' => $media->getUrl(), // Get the full URL of the uploaded media
        ]);
    
        return response()->json([
            'message' => 'Profile picture uploaded successfully.',
            'profile_pic' => $user->profile_pic, // Updated column
        ]);
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
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'phone_no' => $user->phone_no,
            'profile_pic' => $user->profile_pic, // Accessor returns full URL
            'role' => $user->role,
        ]);
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


    public function apiupdate(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Log what the backend receives
        Log::info('Received update request:', $request->all());
    
        // Validate request input
        $validated = $request->validate([
            'username' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone_no' => 'nullable|string|max:20',
            'profile_pic' => 'nullable|string',
        ]);
    
        // Handle profile picture deletion
        if ($request->filled('profile_pic') && $request->input('profile_pic') === '') {
            Log::info('Deleting profile picture...');
            $user->clearMediaCollection('profile_pictures');
            $validated['profile_pic'] = null; // Clear in database
        }        
    
        // Update user profile fields
        $user->update($validated);
    
        // Log after update
        Log::info('Updated user profile:', $user->toArray());
    
        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ]);
    }        
    
    
    public function deleteProfilePicture(Request $request): JsonResponse
    {
        $user = $request->user();
    
        // Check if the profile_pic field in the database is already null
        if ($user->profile_pic === null) {
            return response()->json([
                'message' => 'No profile picture found to delete.',
            ], 404);
        }
    
        // Explicitly delete all media files in the collection
        $mediaItems = $user->getMedia('profile_pictures');
        if ($mediaItems->isNotEmpty()) {
            foreach ($mediaItems as $media) {
                $media->delete(); // Delete media from storage and database
            }
        }
    
        // Set the profile_pic column in the database to null
        $user->update(['profile_pic' => null]);
    
        return response()->json([
            'message' => 'Profile picture deleted successfully.',
            'profile_pic' => null,
        ]);
    }
    
}
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

        if ($user->role === 'admin') {
            return view('admin.profile.edit', ['user' => $user]);
        }

        if ($user->role === 'supervisor') {
            return view('supervisor.profile.edit', ['user' => $user]);
        }

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
    
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Accept only jpg, jpeg, png
        ], [
            'profile_pic.required' => 'Profile picture is required.',
            'profile_pic.image' => 'The file must be an image.',
            'profile_pic.mimes' => 'The file must be a jpg, jpeg, or png image.',
            'profile_pic.max' => 'The image size must not exceed 2MB.',
        ]);
    
        try {
            // Clear existing profile picture
            $user->clearMediaCollection('profile_pictures');
    
            // Store the new profile picture
            $media = $user->addMediaFromRequest('profile_pic')->toMediaCollection('profile_pictures');
            $profilePicUrl = $media->getUrl();
    
            // Update profile_pic in the users table
            $user->update(['profile_pic' => $profilePicUrl]);
    
            // If the user is a cleaner, update the cleaners table as well
            if ($user->role === 'cleaner') {
                $cleaner = Cleaner::where('user_id', $user->id)->first();
                if ($cleaner) {
                    $cleaner->update(['profile_pic' => $user->profile_pic]); // Save the URL, not raw data
                }
            }            
    
            return response()->json([
                'message' => 'Profile picture uploaded successfully.',
                'profile_pic' => $profilePicUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading profile picture: ' . $e->getMessage());
            return response()->json(['message' => 'Error uploading profile picture.'], 500);
        }
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
            'profile_pic' => $user->profile_pic ?: asset('storage/profile_pic/default.webp'),
            'role' => $user->role,
            'building' => $user->building,
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

        try {
            // Handle profile picture upload
            if ($request->hasFile('profile_pic')) {
                $user->clearMediaCollection('profile_pictures');
                $media = $user->addMediaFromRequest('profile_pic')->toMediaCollection('profile_pictures');
                $user->profile_pic = $media->getUrl();
            }

            // Update user with validated data
            $user->fill($request->validated());
            $user->save();

            Log::info('User after save:', [$user->toArray()]);
            return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', 'Error updating profile.');
        }
    }

    public function apiupdate(Request $request): JsonResponse
    {
        $user = $request->user();

        Log::info('Received update request:', $request->all());

        $validated = $request->validate([
            'username' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone_no' => 'nullable|string|max:20',
            'profile_pic' => 'nullable|string',
        ]);

        try {
            if ($request->filled('profile_pic') && $request->input('profile_pic') === '') {
                Log::info('Deleting profile picture...');
                $user->clearMediaCollection('profile_pictures');
                $validated['profile_pic'] = null;
            }

            $user->update($validated);
            Log::info('Updated user profile:', $user->toArray());

            return response()->json([
                'message' => 'Profile updated successfully.',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating profile.'], 500);
        }
    }

    public function deleteProfilePicture(Request $request): JsonResponse
    {
        $user = $request->user();
    
        try {
            $user->clearMediaCollection('profile_pictures');
            $user->update(['profile_pic' => null]);
    
            return response()->json([
                'message' => 'Profile picture deleted successfully.',
                'profile_pic' => $user->profile_pic, // Default fallback is handled in the accessor
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting profile picture: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting profile picture.'], 500);
        }
    }    
    
}

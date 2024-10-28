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
            return view('profile.edit', [
                'user' => $request->user(),
            ]);
        }

            /**
         * Return the logged-in user's profile information as JSON.
         */
        public function show(Request $request): JsonResponse
        {
            $user = $request->user();
            return response()->json($user);
        }


        public function storeProfilePicture(Request $request)
        {
            if ($request->hasFile('profile_pic')) {
                // Log the incoming request data
                \Log::info('Incoming request data:', $request->all());
        
                // Store the file in the 'public/profile_pics' directory
                $path = $request->file('profile_pic')->store('profile_pics', 'public');
        
                // Log the stored path for debugging
                \Log::info('Stored file path:', [$path]); // This should log something like 'profile_pics/php1293.tmp'
        
                // Return only the relative path for database storage
                return $path; // Return the relative path
            }
            return null; // Return null if no file was uploaded
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
        //API functions

        public function getProfile(Request $request) : JsonResponse
        {
            $user = Auth::user();
        
            if ($user) {
                return response()->json([
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone_no' => $user->phone_no,
                    'profile_pic' => $user->profile_pic, 
                ]);
            }
        
            return response()->json(['message' => 'User not found'], 404);
        }
        public function update(ProfileUpdateRequest $request): JsonResponse
        {
            Log::info('Profile update request received:', $request->all());
            $user = $request->user();
        
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }
        
            Log::info('Validated data:', $request->validated());
        
            // Handle profile picture upload using the media library
            if ($request->hasFile('profile_pic')) {
                // Clear any previous profile picture
                $user->clearMediaCollection('profile_pictures');
        
                // Add the new profile picture
                $media = $user->addMediaFromRequest('profile_pic')
                              ->toMediaCollection('profile_pictures', 'public');
        
                Log::info('Profile picture uploaded:', ['media' => $media]);
        
                // Set the profile_pic field to the URL of the uploaded image
                $user->profile_pic = $media->getUrl();
            } else {
                Log::info('No profile picture uploaded');
            }
        
            // Update user with validated data
            $user->fill($request->validated());
            $user->save();
            Log::info('User after save:', [$user->toArray()]);
        
            // Return updated user info including profile picture
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_no' => $user->phone_no,
                    'profile_pic' => $user->profile_pic ? $user->profile_pic : null, // Directly using URL
                    'role' => $user->role,
                    'building' => $user->building,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ]
            ], 200);
        }
        
        
        
    }
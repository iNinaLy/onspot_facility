<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import Hash for password checking
use App\Models\User;

class AuthController extends Controller
{
  public function login(Request $request)
    {
        // Validation logic for cleaner login
        $request->validate([
            'username' => 'required|string', // Only require username for cleaner
            'password' => 'required|string',
        ]);

        // Retrieve credentials from the request
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        // Attempt to find the cleaner user by username
        $user = User::where('username', $credentials['username'])->first();

        // Check if the user exists and verify password
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Authentication passed
            Auth::login($user); // Log the user in

            // Generate token
            $token = $user->createToken('OnSpot Facility')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
                'role' => 'cleaner', // Return the role of the user
            ], 200);
        } else {
            // Authentication failed
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        // Revoke the token for the user
        if ($user) {
            $user->tokens()->delete(); // Deletes all tokens
            return response()->json(['message' => 'Successfully logged out.'], 200);
        }

        return response()->json(['message' => 'Logout failed.'], 500);
    }

}

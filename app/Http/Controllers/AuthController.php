<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming registration data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Hash the password
        ]);

        // Generate a token for the newly registered user
        $token = $user->createToken('YourAppName')->plainTextToken;

        // Return the user and the token in the response
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    //login method
    public function login(Request $request)
    {
        // Validate that 'login' and 'password' fields are present
        $request->validate([
            'login' => 'required',  // 'login' will hold either email or username
            'password' => 'required',
        ]);
    
        // Check if the provided login field is an email or username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    
        // Prepare credentials array based on whether it's email or username
        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];
    
        // Attempt to log the user in
        if (!Auth::attempt($credentials)) {
            // Return a 401 Unauthorized response when login fails
            return response()->json([
                'message' => 'Invalid login credentials.',
            ], 401);
        }
    
        $user = Auth::user();
        // Generate token for the authenticated user
        $token = $user->createToken('YourAppName')->plainTextToken;
    
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);  // Status 200 OK for successful login
    }

    // Logout method
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out.']);
    }
}

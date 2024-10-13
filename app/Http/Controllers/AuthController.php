<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
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
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Generate token for the authenticated user
            $token = $user->createToken('YourAppName')->plainTextToken;
    
            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }
    
        // If login fails, throw validation error
        throw ValidationException::withMessages([
            'login' => ['The provided credentials are incorrect.'],
        ]);
    }

    // Logout method
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out.']);
    }
}

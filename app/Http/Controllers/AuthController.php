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
                'username' => 'required|string|max:255|unique:users', 
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone_no' => 'nullable|string|max:255', 
                'role' => 'required|string', 
            ]);
        
            // Create the user
            $user = \App\Models\User::create([
                'username' => $request->username, // Optional username
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password), // Hash the password
                'phone_no' => $request->phone_no, // Optional phone number
                'role' => $request->role, // Set the role from the request data
            ]);
        
            // Generate a token for the newly registered user
            $token = $user->createToken('YourAppName')->plainTextToken;
        
            // Return the user and the token in the response, including username and phone_no
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username, // Include username
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_no' => $user->phone_no, // Include phone_no
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'token' => $token,
            ], 201);
        }
        
    

    //login method
    public function login(Request $request)
    {
        // check if 'login' and 'password' fields are present
        $request->validate([
            'login' => 'required', 
            'password' => 'required',
        ]);
    
        // check if it is an email or username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    
        // credentials array | email or username
        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];
    
        //  log the user in
        if (!Auth::attempt($credentials)) {
            // unauthorized response
            return response()->json([
                'message' => 'Invalid login credentials.',
            ], 401);
        }
    
        $user = Auth::user();
        // token for the authenticated user
        $token = $user->createToken('YourAppName')->plainTextToken;
    
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);  
    }

    //logout method
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out.']);
    }
}

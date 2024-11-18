<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\NotificationToken;

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
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone_no' => $request->phone_no,
            'role' => $request->role,
        ]);

        // Generate a token for the newly registered user
        $token = $user->createToken('YourAppName')->plainTextToken;

        // Return the user and the token in the response
        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        // Validate 'login' and 'password' fields
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        // Determine if the login is an email or username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Credentials array for email or username
        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        // Attempt to log the user in
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login credentials.',
            ], 401);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if the user's role is 'admin' or 'supervisor'
        if (!in_array($user->role, ['admin', 'supervisor'])) {
            Auth::logout();
            return response()->json([
                'message' => 'Access denied. Only admins and supervisors can log in.',
            ], 403);
        }

        // Create a token for the authenticated user
        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function storeNotificationToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required',
            'device_id' => 'required',
            'device_type' => 'required',
        ]);

        NotificationToken::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'device_id' => $request->device_id,
            ],
            [
                'device_token' => $request->device_token,
                'device_type' => $request->device_type,
            ]
        );

        return response()->json(['message' => 'Device token saved successfully.']);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json(['message' => 'Successfully logged out.']);
    }
}

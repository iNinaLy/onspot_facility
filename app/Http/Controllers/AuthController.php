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
                'device_token' => 'required_with:device_id,device_type|string',
                'device_id' => 'required_with:device_token|string',
                'device_type' => 'required_with:device_token|string|in:android,ios,web',
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
        
            // Save FCM token if provided
            if ($request->filled(['device_token', 'device_id', 'device_type'])) {
                NotificationToken::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'device_id' => $request->device_id,
                    ],
                    [
                        'device_token' => $request->device_token,
                        'device_type' => $request->device_type,
                    ]
                );
            }
        
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
            $request->validate([
                'login' => 'required',
                'password' => 'required',
                'device_token' => 'required_with:device_id,device_type', // FCM token is required if device info is provided
                'device_id' => 'required_with:device_token',
                'device_type' => 'required_with:device_token|in:android,ios,web',
            ]);
        
            $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
            $credentials = [
                $loginType => $request->login,
                'password' => $request->password,
            ];
        
            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid login credentials.'], 401);
            }
        
            $user = Auth::user();
            $token = $user->createToken('YourAppName')->plainTextToken;
        
            // Save FCM token if provided
            if ($request->filled(['device_token', 'device_id', 'device_type'])) {
                $this->storeNotificationToken($request);
            }
        
            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);
        }
            

    //logout method
    public function logout(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string', // Ensure the device ID is provided for cleanup
        ]);
    
        $user = Auth::user();
    
        // Delete the FCM token for the specific device
        NotificationToken::where('user_id', $user->id)
            ->where('device_id', $request->device_id)
            ->delete();
    
        // Revoke all authentication tokens for the user
        $user->tokens()->delete();
    
        return response()->json(['message' => 'Successfully logged out.'], 200);
    }
    

    public function storeNotificationToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_id' => 'required|string',
            'device_type' => 'required|string|in:android,ios,web', // Restrict valid device types
        ]);
    
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $token = NotificationToken::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'device_id' => $request->device_id,
            ],
            [
                'device_token' => $request->device_token,
                'device_type' => $request->device_type,
            ]
        );
    
        return response()->json([
            'message' => 'Device token saved successfully.',
            'token' => $token,
        ], 200);
    }
    

}

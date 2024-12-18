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
        \Log::info('Starting registration process');
    
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
            'building' => 'nullable|string|max:255', 

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
    
        \Log::info('User created successfully: ' . $user->id);
    
        // If the role is 'cleaner', create a corresponding cleaner record
        if ($user->role === 'cleaner') {
            try {
                \Log::info('Attempting to create cleaner record for user: ' . $user->id);
                \App\Models\Cleaner::create([
                    'user_id' => $user->id,
                    'cleaner_username' => $user->username,
                    'cleaner_name' => $user->name,
                    'cleaner_phoneNo' => $user->phone_no,
                    'status' => 'available',
                    'cleaner_password' => bcrypt($request->password), 
                    'building' => $request->input('building', 'default_building'), // Default if missing
                ]);
                \Log::info('Cleaner record created successfully for user: ' . $user->id);
            } catch (\Exception $e) {
                \Log::error('Failed to create cleaner record: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to create cleaner record.'], 500);
            }
        }
        
    
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
        \Log::info('Incoming Request', $request->all());
    
        $request->validate([
            'device_token' => 'required|string',
            'device_id' => 'required|string',
            'device_type' => 'required|string|in:android,ios,web',
        ]);
    
        try {
            // Use updateOrCreate to handle duplicates
            $token = \App\Models\NotificationToken::updateOrCreate(
                [
                    'device_token' => $request->device_token, // Match by device_token
                ],
                [
                    'user_id' => auth()->id(), // Update these fields if device_token exists
                    'device_id' => $request->device_id,
                    'device_type' => $request->device_type,
                ]
            );
    
            \Log::info('Device token saved successfully', ['token' => $token]);
    
            return response()->json(['message' => 'Device token saved successfully.'], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to save device token: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to save device token.', 'error' => $e->getMessage()], 500);
        }
    }
    

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    
        $user = DB::table('users')->where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email not found.'], 404);
        }
    
        $code = rand(100000, 999999); // Generate a 6-digit code
    
        try {
            // Log code generation
            Log::info("Generated reset code for {$request->email}: $code");
    
            // Store the code in the password_resets table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'code' => $code,
                    'created_at' => now()
                ]
            );
    
            // Send the code via email
            Mail::raw("Your password reset code is: $code", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Password Reset Code');
            });
    
            return response()->json(['message' => 'Reset code sent successfully.']);
        } catch (\Exception $e) {
            Log::error('Error sending reset code: ' . $e->getMessage());
            return response()->json(['message' => 'Unable to send reset link. Please check your email.'], 500);
        }
    }
    


    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric|digits:6',
            'password' => 'required|confirmed|min:8'
        ]);
    
        Log::info("Verifying reset code for {$request->email} with code {$request->code}");
    
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();
    
        if (!$resetRecord) {
            Log::warning("Invalid code or email for {$request->email}");
            return response()->json(['message' => 'Invalid code or email.'], 400);
        }
    
        // Reset the password
        DB::table('users')->where('email', $request->email)->update([
            'password' => bcrypt($request->password)
        ]);
    
        // Log successful reset
        Log::info("Password reset successfully for {$request->email}");
    
        // Delete the reset code from the database
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
    
        return response()->json(['message' => 'Password has been reset successfully.']);
    }


}
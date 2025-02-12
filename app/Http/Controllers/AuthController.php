<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\NotificationToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\SupabaseService;

class AuthController extends Controller
    {

    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
         $this->supabaseService = $supabaseService;
    }

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
            'email_verified_at' => now(),
        ]);
    
        \Log::info('User created successfully: ' . $user->id);
    
        // If the role is 'cleaner', create a corresponding cleaner record
        if ($user->role === 'cleaner') {
            try {
                \Log::info('Attempting to create cleaner record for user: ' . $user->id);
                \Log::info('Cleaner data: ', $request->only(['username', 'name', 'phone_no', 'building']));

                \App\Models\Cleaner::create([
                    'user_id' => $user->id,
                    'cleaner_username' => $user->username,
                    'cleaner_name' => $user->name,
                    'cleaner_phoneNo' => $user->phone_no,
                    'status' => 'available',
                    'cleaner_password' => bcrypt($request->password), 
                    'building' => $request->input('building', 'building A'), // Default if missing
                ]);
                \Log::info('Cleaner record created successfully for user: ' . $user->id);
            } catch (\Exception $e) {
                \Log::error('Failed to create cleaner record: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to create cleaner record.'], 500);
            }
        }
        
            // Send a welcome email
            try {
                Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
                    $message->to($user->email)->subject('Welcome to OnSpot Facility');
                });
                \Log::info('Welcome email sent to: ' . $user->email);
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email: ' . $e->getMessage());
            }
    
        // Generate a token for the newly registered user
        $token = $user->createToken('OnSpot Facility')->plainTextToken;
    
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
            $token = $user->createToken('OnSpot Facility')->plainTextToken;
        
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
    
        // ✅ Step 1: Validate Input Data
        $request->validate([
            'device_token' => 'required|string',
            'device_id' => 'required|string',
            'device_type' => 'required|string|in:android,ios,web',
        ]);
    
        try {
            // ✅ Step 2: Save Token in MySQL and Get the ID & Timestamps
            $token = \App\Models\NotificationToken::updateOrCreate(
                [
                    'device_id' => $request->device_id,
                    'device_type' => $request->device_type,
                ],
                [
                    'device_token' => $request->device_token,
                    'user_id' => auth()->id(),
                ]
            );
    
            \Log::info('Device token saved successfully in MySQL', ['token' => $token]);
    
            // ✅ Step 3: Store Data in Supabase (Including Timestamps)
            $this->supabaseService->store('notification_tokens', [
                'id' => $token->id, // ✅ Ensure MySQL ID is used in Supabase
                'user_id' => auth()->id(),
                'device_id' => $request->device_id,
                'device_token' => $request->device_token,
                'device_type' => $request->device_type,
                'created_at' => $token->created_at->toISOString(), // ✅ Send created_at
                'updated_at' => $token->updated_at->toISOString(), // ✅ Send updated_at
            ]);
    
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
            // Store the code in the database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'code' => $code,
                    'created_at' => now()
                ]
            );
    
            // Send the email using the Blade template
            Mail::send('emails.reset_code', ['code' => $code], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Password Reset Code');
            });
    
            return response()->json(['message' => 'Reset code sent successfully.']);
        } catch (\Exception $e) {
            Log::error('Error sending reset code: ' . $e->getMessage());
            return response()->json(['message' => 'Unable to send reset code. Please try again later.'], 500);
        }
    }
    

    public function verifyResetCode(Request $request)
    {
        \Log::info('Verify reset code request received.', $request->all());
    
        try {
            $request->validate([
                'email' => 'required|email',
                'code' => 'required|numeric|digits:6',
            ]);
    
            \Log::info('Validation passed.');
    
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('code', $request->code)
                ->where('created_at', '>', now()->subMinutes(15)) // Code expires after 15 minutes
                ->first();
    
            if (!$resetRecord) {
                \Log::warning('Kata laluan tidak sah.', $request->all());
                return response()->json(['message' => 'Kata laluan tidak sah.'], 400);
            }
    
            \Log::info('Reset code verified successfully for email: ' . $request->email);
            return response()->json(['message' => 'Reset code verified successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error during reset code verification: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred during reset code verification.'], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        \Log::info('Password reset request received.', $request->all());
    
        try {
            // Validate input
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed', // Ensure the password confirmation is required
            ], [
                'password.confirmed' => 'The password confirmation does not match.', // Custom error message
            ]);
    
            \Log::info('Validation passed.');
    
            // Check if user exists
            $user = DB::table('users')->where('email', $request->email)->first();
            if (!$user) {
                \Log::warning('Email not found for password reset.', $request->all());
                return response()->json(['message' => 'Email not found.'], 404);
            }
    
            // Update the password
            DB::table('users')->where('email', $request->email)->update([
                'password' => Hash::make($request->password),
            ]);
    
            \Log::info('Password reset successfully for email: ' . $request->email);
    
            // Return success response
            return response()->json(['message' => 'Password has been reset successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error during password reset: ' . $e->getMessage());
            return response()->json(['message' => $e->errors()], 422); // Validation error with 422 response
        } catch (\Exception $e) {
            \Log::error('Error during password reset: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred during password reset.'], 500);
        }
    }    

}
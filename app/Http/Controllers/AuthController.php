<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;



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
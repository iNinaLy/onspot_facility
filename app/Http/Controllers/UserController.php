<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Import the DB facade here
use App\Models\User;
use App\Models\Supervisor;
use App\Models\Officer;
use App\Models\Cleaner;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();  // Retrieve all users
        return view('admin.users', compact('users'));  // Return the user list view
    }

    // Display the form to create a new user
    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_no' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:officer,supervisor,cleaner',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle profile picture if uploaded
        $profilePicPath = null;
        if ($request->hasFile('profile_pic')) {
            $profilePicPath = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        // Create new user with fillable properties (stored in 'users' table)
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'profile_pic' => $profilePicPath,
            'email_verified_at' => now(),
        ]);

        // Store the role-specific information in their corresponding table
        if ($user->role == 'supervisor') {
            DB::table('supervisors')->insert([
                's_id' => $user->id,
                's_email' => $user->email,
                's_pass' => $user->password,
                's_name' => $user->name,
                's_phoneNo' => $user->phone_no,
                's_username' => $user->username,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($user->role == 'officer') {
            DB::table('officers')->insert([
                'id' => $user->id,
                'officer_email' => $user->email,
                'officer_pass' => $user->password,
                'officer_name' => $user->name,
                'officer_phoneNo' => $user->phone_no,
                'officer_username' => $user->username,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($user->role == 'cleaner') {
            DB::table('cleaners')->insert([
                'id' => $user->id,
                'cleaner_name' => $user->name,
                'cleaner_phoneNo' => $user->phone_no,
                'cleaner_username' => $user->username,
                'cleaner_password' => $user->password,
                'status' => 'Active', // Default status, you can modify this as needed
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Redirect back with success message
        return redirect()->route('admin.users.create')->with('success', 'User added successfully!');
    }

    
}
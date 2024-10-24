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
        // Validation rules
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_no' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed', // Confirmed means it must match the password_confirmation field
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Optional profile pic validation
            'role' => 'required|string|in:officer,supervisor,cleaner',
            
                'building' => 'required|in:Building A,Building B,Building C', // Update validation rule
       
            
        ]);

        // Create the user
        $user = new User();
        $user->username = $validatedData['username'];
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->phone_no = $validatedData['phone_no'];
        $user->password = Hash::make($validatedData['password']); // Hash the password
        $user->role = $validatedData['role'];
        $user->building = $validatedData['building'];

        // Handle file upload for profile picture
        if ($request->hasFile('profile_pic')) {
            $user->profile_pic = $request->file('profile_pic')->store('profile_pics', 'public');
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
        if ($user->role == 'cleaner') {
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

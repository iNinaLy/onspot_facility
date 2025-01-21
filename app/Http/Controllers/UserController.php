<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Cleaner;


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
    $validatedData = $request->validate([
        'username'    => 'required|string|max:255|unique:users,username',
        'name'        => 'required|string|max:255',
        'email'       => 'required|email|unique:users,email',
        'phone_no'    => 'required|string|max:15',
        'password'    => 'required|string|min:8|confirmed',
        'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'role'        => 'required|string|in:officer,supervisor,cleaner',
        'building'    => 'required|string|in:Building A,Building B,Building C',
    ]);

    $profilePicPath = null;
    if ($request->hasFile('profile_pic')) {
        $profilePicPath = $request->file('profile_pic')->store('profile_pics', 'public');
    }

    $user = User::create([
        'username'          => $validatedData['username'],
        'name'              => $validatedData['name'],
        'email'             => $validatedData['email'],
        'phone_no'          => $validatedData['phone_no'],
        'password'          => Hash::make($validatedData['password']),
        'role'              => $validatedData['role'],
        'building'          => $validatedData['building'],
        'profile_pic'       => $profilePicPath,
        'email_verified_at' => now(),
    ]);

    if ($validatedData['role'] === 'cleaner') {
        Cleaner::create([
            'user_id'          => $user->id,                        
            'cleaner_name'     => $validatedData['name'],
            'cleaner_phoneNo'  => $validatedData['phone_no'],
            'profile_pic'      => $profilePicPath,
            'cleaner_username' => $validatedData['username'],
            'cleaner_password' => $user->password,                  
            'status'           => 'available',                      
            'building'         => $validatedData['building'],
        ]);
    }

    // Redirect back with a success message to avoid blank page
    return redirect()->route('admin.users.create')->with('success', 'New user added.');
}


    
}

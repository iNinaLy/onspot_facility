<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Supervisor;
use App\Models\Officer;
use App\Models\Cleaner;
>>>>>>> origin/of
use Illuminate\Http\Request;

class UserController extends Controller
{
<<<<<<< HEAD
    //
=======
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
        // Validate input
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_no' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:officer,supervisor,cleaner',
            'profile_pic' => 'nullable|image|max:2048',  // Optional profile picture
        ]);

        // Handle profile picture upload if provided
        $profilePicPath = null;
        if ($request->hasFile('profile_pic')) {
            $profilePicPath = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        // Create user in 'users' table
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'password' => Hash::make($request->password),  // Hash the password for security
            'role' => $request->role,
            'profile_pic' => $profilePicPath,
        ]);

        // Store user data in the corresponding role table
        $this->storeRoleSpecificData($request, $user);

        // Redirect to the user list page with a success message
        return redirect()->route('admin.users.create')->with('success', 'User added successfully.');
    }

    // Handle role-specific data storage
    protected function storeRoleSpecificData($request, $user)
    {
        $hashedPassword = Hash::make($request->password);
        switch ($request->role) {
            case 'officer':
                Officer::create([
                    'officer_name' => $request->name,
                    'officer_email' => $request->email,
                    'officer_phoneNo' => $request->phone_no,
                    'officer_pass' => $hashedPassword, // Use hashed password
                ]);
                break;

            case 'supervisor':
                Supervisor::create([
                    's_name' => $request->name,
                    's_email' => $request->email,
                    's_phoneNo' => $request->phone_no,
                    's_pass' => $hashedPassword, // Use hashed password
                ]);
                break;

            case 'cleaner':
                Cleaner::create([
                    'cleaner_name' => $request->name,
                    'cleaner_phoneNo' => $request->phone_no,
                    'username' => $request->username,  // Ensure that username is provided
                    'password' => $hashedPassword, // Use hashed password for security
                    'status' => 'active', // Default status
                ]);
                break;
        }
    }
>>>>>>> origin/of
}

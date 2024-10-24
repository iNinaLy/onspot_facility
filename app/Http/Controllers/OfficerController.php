<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OfficerController extends Controller
{
    // Fetch officers from the users table
    public function index(Request $request)
{
    $search = $request->query('search');

    // Apply search query to paginate officers
    $officers = Officer::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('phone_no', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
        })
        ->paginate(10); // This ensures pagination is used

    return view('admin.officers.index', compact('officers'));
}



    public function create()
    {
        return view('admin.officers.create');
    }

    // Store a new officer
    public function store(Request $request)
    {
        $request->validate([
            'officer_name' => 'required|string|max:255',
            'officer_email' => 'required|email|unique:users,email',
            'officer_phoneNo' => 'required|string|max:20',
            'officer_pass' => 'required|string|min:8'
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->officer_name,
                'email' => $request->officer_email,
                'password' => Hash::make($request->officer_pass),
                'role' => 'officer',
            ]);

            Officer::create([
                'user_id' => $user->id,
                'officer_name' => $request->officer_name,
                'officer_email' => $request->officer_email,
                'officer_phoneNo' => $request->officer_phoneNo,
            ]);
        });

        return redirect()->route('admin.officers')->with('success', 'Officer added successfully.');
    }



    // Method to display the officer edit form
    public function editOfficer($id)
    {
        // Fetch the officer by ID from the users table where role is 'officer'
        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
        
        // Return the view with officer data
        return view('admin.officers.edit', compact('officer'));
    }

    // Method to update the officer
    public function updateOfficer(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id, // Ensure unique email except the current user
            'phone_no' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Find the officer (user) by ID
        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();

        // Update officer data in the `users` table
        $officer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
        ]);

        // If a password is provided, hash it and update the officer's password
        if ($request->filled('password')) {
            $officer->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Handle profile picture upload if provided
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $path = $file->store('profile_pics', 'public');
            $officer->update(['profile_pic' => $path]);
        }

        // Redirect back to the officers index with a success message
        return redirect()->route('admin.officers')->with('success', 'Officer updated successfully!');
    }
    
  // Delete an officer
    public function destroy($id)
    {
        // Find and delete the officer
        $officer = User::where('role', 'officer')->findOrFail($id);
        $officer->delete();

        return redirect()->route('admin.officers')->with('success', 'Officer deleted successfully.');
    }
}

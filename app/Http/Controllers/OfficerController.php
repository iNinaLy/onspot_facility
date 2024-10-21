<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Officer;
use Illuminate\Http\Request;

class OfficerController extends Controller
{
    // Get all officers
    public function index(Request $request)
    {
        $letter = $request->input('filter', null);

        // Filter officers by the starting letter if a letter is selected
        if ($letter) {
            $officers = Officer::where('officer_name', 'LIKE', "$letter%")->get();
        } else {
            $officers = Officer::all();
        }

        return view('admin.officers.index', compact('officers'));
    }

    // Store a new officer
    public function storeOfficer(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'officer_name' => 'required|string|max:255',
            'officer_email' => 'required|email|unique:users,email',
            'officer_phoneNo' => 'required|string|max:20',
            'officer_pass' => 'required|string|min:8'
        ]);

        // Use a transaction to ensure that both records are stored correctly
        DB::transaction(function () use ($request) {
            // Create user entry in users table
            $user = User::create([
                'name' => $request->officer_name,
                'email' => $request->officer_email,
                'password' => Hash::make($request->officer_pass),
                'role' => 'officer',  // assuming there’s a role column in users table
            ]);

            // Create officer entry in officers table, linked to user_id
            Officer::create([
                'user_id' => $user->id,
                'officer_name' => $request->officer_name,
                'officer_email' => $request->officer_email,
                'officer_phoneNo' => $request->officer_phoneNo,
                'officer_pass' => Hash::make($request->officer_pass),  // Provide a hashed password
            ]);
            
        });

        // Redirect back with success message
        return redirect()->route('admin.officers')->with('success', 'Officer added successfully.');
    }

    // Show a single officer
    public function show($id)
    {
        $officer = Officer::findOrFail($id);
        return view('admin.officers.show', compact('officer'));
    }

    // Update an officer
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'officer_name' => 'required|string|max:255',
            'officer_email' => 'required|email|unique:officers,officer_email,' . $id,
            'officer_phoneNo' => 'required|string|max:20',
            'officer_pass' => 'nullable|string|min:8'  // Optional password update
        ]);

        // Find the officer and update fields
        $officer = Officer::findOrFail($id);
        $officer->officer_name = $request->officer_name;
        $officer->officer_email = $request->officer_email;
        $officer->officer_phoneNo = $request->officer_phoneNo;

        // Update password if provided
        if ($request->filled('officer_pass')) {
            $officer->officer_pass = Hash::make($request->officer_pass);
        }

        $officer->save();

        return redirect()->route('admin.officers')->with('success', 'Officer updated successfully.');
    }

    // Delete an officer
    public function destroy($id)
    {
        // Find the officer and delete
        $officer = Officer::findOrFail($id);
        $officer->delete();

        return redirect()->route('admin.officers')->with('success', 'Officer deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CleanerController extends Controller
{
    // Display all cleaners with additional counts
    public function index()
    {
        Log::info('Cleaners index accessed');

        $cleaners = Cleaner::all(); // Retrieve all cleaners
        $availableCount = Cleaner::where('status', 'available')->count();
        $unavailableCount = Cleaner::where('status', 'unavailable')->count();
        $totalCleaners = $cleaners->count();

        return view('supervisor.cleaners.index', compact('cleaners', 'totalCleaners', 'availableCount', 'unavailableCount'));
    }

    // Search and display paginated cleaners
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cleaner_name' => 'required|string|max:255',
            'cleaner_phoneNo' => 'required|string|max:15',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:available,unavailable',
        ]);
    
        // Create a user for the cleaner
        $user = User::create([
            'name' => $validated['cleaner_name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'phone_no' => $validated['cleaner_phoneNo'],
            'role' => 'cleaner',
        ]);
    
        // Create the cleaner entry linked to the user
        $cleaner = Cleaner::create([
            'user_id' => $user->id,
            'cleaner_name' => $validated['cleaner_name'],
            'cleaner_phoneNo' => $validated['cleaner_phoneNo'],
            'status' => $validated['status'],
        ]);
    
        // Handle profile picture upload using Spatie Media Library
        if ($request->hasFile('profile_pic')) {
            $cleaner->addMediaFromRequest('profile_pic')
                    ->toMediaCollection('profile_pictures', 'public');
        }
    
        return redirect()->back()->with('success', 'Cleaner added successfully');
    }
    

    // Show the form to edit cleaner information
    public function edit($id)
    {
        $cleaner = Cleaner::findOrFail($id);
        return view('admin.cleaners.edit', compact('cleaner'));
    }

    // Update cleaner information
    public function update(Request $request, $id)
    {
        // Retrieve the cleaner
        $cleaner = Cleaner::findOrFail($id);

        // Initialize validation rules
        $validationRules = [
            'cleaner_name' => 'sometimes|required|string|max:255',
            'cleaner_phoneNo' => 'sometimes|required|string|max:15',
            'username' => 'required|string|max:255|unique:cleaners,cleaner_username,' . $cleaner->id,
            'phone_no' => 'required|string|max:20',
            'password' => 'nullable|confirmed|min:8',
        ];

        // Only allow admins to change the status field
        if (Auth::user()->role === 'admin') {
            $validationRules['status'] = 'required|in:available,unavailable';
        }

        // Validate the request based on the defined rules
        $validated = $request->validate($validationRules);

        // Only update fields that are provided in the request and have changed
        if ($request->filled('cleaner_name') && $request->cleaner_name !== $cleaner->cleaner_name) {
            $cleaner->cleaner_name = $validated['cleaner_name'];
        }

        if ($request->filled('cleaner_phoneNo') && $request->cleaner_phoneNo !== $cleaner->cleaner_phoneNo) {
            $cleaner->cleaner_phoneNo = $validated['cleaner_phoneNo'];
        }

        if ($request->filled('username') && $request->username !== $cleaner->cleaner_username) {
            $cleaner->cleaner_username = $validated['username'];
        }

        if ($request->filled('phone_no') && $request->phone_no !== $cleaner->cleaner_phoneNo) {
            $cleaner->cleaner_phoneNo = $validated['phone_no'];
        }

        if ($request->filled('password')) {
            $cleaner->cleaner_password = bcrypt($validated['password']);
        }

        // Update status only if the user is an admin and the status field is provided in the request
        if (Auth::user()->role === 'admin' && $request->filled('status') && $request->status !== $cleaner->status) {
            $cleaner->status = $validated['status'];
        }

        // Handle profile picture upload using Spatie Media Library
        if ($request->hasFile('profile_pic')) {
            $cleaner->clearMediaCollection('profile_pictures'); // Remove previous image
            $cleaner->addMediaFromRequest('profile_pic')
                    ->toMediaCollection('profile_pictures', 'public');
        }

        // Save the cleaner changes
        $cleaner->save();

        return redirect()->route('admin.cleaners.edit', $cleaner->id)
                        ->with('success', 'Cleaner details updated successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        // Ensure only admins can change status
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Only admins can change cleaner status.');
        }

        // Find the cleaner
        $cleaner = Cleaner::findOrFail($id);

        // Validate the status field
        $validated = $request->validate([
            'status' => 'required|in:available,unavailable',
        ]);

        // Update the status
        $cleaner->status = $validated['status'];
        $cleaner->save();

        // Redirect back with success message
        return redirect()->route('admin.cleaners')->with('success', 'Cleaner status updated successfully.');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|confirmed|min:8',
        ]);

        $cleaner = Cleaner::findOrFail($id);
        $cleaner->password = Hash::make($request->new_password);
        $cleaner->save();

        return redirect()->route('admin.cleaners.edit', $id)->with('status', 'Password has been reset successfully.');
    }


    // Delete a cleaner
    public function destroy($id)
    {
        $cleaner = Cleaner::findOrFail($id);
    
        // Delete the associated user
        User::where('id', $cleaner->user_id)->delete();
    
        // Delete the cleaner
        $cleaner->delete();
    
        return response()->json(['message' => 'Cleaner deleted successfully'], 200);
    }
    

    
}
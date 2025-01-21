<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class CleanerController extends Controller
{
    public function index(Request $request)
    {
        // Example: however you fetch your data
        $search = $request->input('search');
        
        // Summaries
        $totalCleaners    = Cleaner::count();
        $availableCount   = Cleaner::where('status', 'available')->count();
        $unavailableCount = Cleaner::where('status', 'unavailable')->count();
        
        $availableCleaners = Cleaner::where('status', 'available')
            ->when($search, fn($q) => $q->where('cleaner_name', 'like', "%{$search}%"))
            ->with(['complaints' => function ($q) {
                $q->where('comp_status', 'ongoing');
            }])
            ->paginate(10, ['*'], 'available_page');
        
        // Query unavailable cleaners
        $unavailableCleaners = Cleaner::where('status', 'unavailable')
            ->when($search, fn($q) => $q->where('cleaner_name', 'like', "%{$search}%"))
            ->with(['complaints' => function ($q) {
                $q->where('comp_status', 'ongoing');
            }])
            ->paginate(5, ['*'], 'unavailable_page');
        
        // Pass all these to the view
        return view('supervisor.cleaners.index', [
            'search'             => $search,
            'totalCleaners'      => $totalCleaners,
            'availableCount'     => $availableCount,
            'unavailableCount'   => $unavailableCount,
            'availableCleaners'  => $availableCleaners,
            'unavailableCleaners'=> $unavailableCleaners,
        ]);
    }
    

    
    public function cleaners(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        // Build the query with search and status filters
        $cleanersQuery = Cleaner::query();

        if ($search) {
            $cleanersQuery->where(function ($query) use ($search) {
                $query->where('cleaner_name', 'LIKE', "%{$search}%")
                      ->orWhere('cleaner_phoneNo', 'LIKE', "%{$search}%")
                      ->orWhere('cleaner_username', 'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $cleanersQuery->where('status', strtolower($status));
        }

        // Paginate the results
        $cleaners = $cleanersQuery->paginate(10);

        // Compute the counts for metrics
        $totalCleaners = Cleaner::count();
        $availableCleaners = Cleaner::where('status', 'available')->count();
        $unavailableCleaners = Cleaner::where('status', 'unavailable')->count();

        // Pass the variables to the view
        return view('admin.cleaners.index', compact(
            'cleaners',
            'search',
            'status',
            'totalCleaners',
            'availableCleaners',
            'unavailableCleaners'
        ));
    }

    public function createCleaner()
    {
        return view('admin.cleaners.create');
    }

    public function editCleaner(Cleaner $cleaner)
    {
        return view('admin.cleaners.edit', compact('cleaner'));
    }

    public function updateCleaner(Request $request, $id)
    {
        // Define validation rules
        $validationRules = [
            'cleaner_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:cleaners,cleaner_username,' . $id . ',id',
            'phone_no' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'building' => 'required|string|in:Building A,Building B,Building C',
        ];

        // Validate the incoming data
        $validated = $request->validate($validationRules);

        // Find the cleaner by ID
        $cleaner = Cleaner::findOrFail($id);

        // Prepare data for update
        $dataToUpdate = [
            'cleaner_name' => $validated['cleaner_name'],
            'cleaner_username' => $validated['username'],
            'cleaner_phoneNo' => $validated['phone_no'],
            'building' => $validated['building'],
        ];

        // If a password is provided, hash it and add to update data
        if ($request->filled('password')) {
            $dataToUpdate['cleaner_password'] = Hash::make($validated['password']);
        }

        // Handle profile picture upload if provided
        if ($request->hasFile('profile_pic')) {
            // Remove previous image if exists
            // Assuming you're using a package like Spatie Media Library
            $cleaner->clearMediaCollection('profile_pictures');

            // Add new profile picture
            $cleaner->addMediaFromRequest('profile_pic')
                    ->toMediaCollection('profile_pictures', 'public');
        }

        // Update the cleaner's data in the database
        $cleaner->update($dataToUpdate);

        // Redirect back to the edit form with a success message
        return redirect()->route('admin.cleaners.edit', $cleaner->id)
                         ->with('success', 'Cleaner details updated successfully!');
    }


    public function resetCleanerPassword(Request $request, $cleaner)
    {
        $request->validate([
            'new_password' => 'required|confirmed|min:8',
        ]);

        $cleaner = Cleaner::findOrFail($cleaner);
        $cleaner->password = Hash::make($request->new_password);
        $cleaner->save();

        return redirect()->route('cleaners.edit', $cleaner)->with('status', 'Password has been reset successfully.');
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

    public function destroyCleaner($id)
    {
        // Find the cleaner by ID or fail if not found
        $cleaner = Cleaner::findOrFail($id);

        // If using Spatie Media Library, clear associated media before deletion
        $cleaner->clearMediaCollection('profile_pictures');

        // Delete the cleaner record from the database
        $cleaner->delete();

        // Redirect back to the cleaners index with a success message
        return redirect()->route('admin.cleaners')->with('success', 'Cleaner deleted successfully!');
    }

}

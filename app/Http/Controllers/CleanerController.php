<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CleanerController extends Controller
{
    // Display all cleaners with additional counts
    public function index()
    {
        Log::info('Cleaners index accessed');

        $cleaners = Cleaner::all(); // Retrieve all cleaners
        $availableCount = Cleaner::where('status', 'available')->count(); // Count available cleaners
        $unavailableCount = Cleaner::where('status', 'unavailable')->count(); // Count unavailable cleaners
        $totalCleaners = $cleaners->count(); // Total number of cleaners

        // Pass all variables to the view
        return view('supervisor.cleaners.index', compact('cleaners', 'totalCleaners', 'availableCount', 'unavailableCount'));
    }

    public function cleaners(Request $request)
    {
        // Fetch counts for cleaners
        $totalCleaners = Cleaner::count();
        $availableCount = Cleaner::where('status', 'available')->count();
        $unavailableCount = Cleaner::where('status', 'unavailable')->count();

        // Handle search functionality
        $search = $request->input('search');
        $cleaners = Cleaner::when($search, function ($query, $search) {
                return $query->where('cleaner_name', 'LIKE', "%{$search}%");
            })
            ->paginate(10); // Pagination control

        // Return the view with necessary data
        return view('supervisor.cleaners.index', compact('totalCleaners', 'availableCount', 'unavailableCount', 'cleaners'));
    }



    // Store a new cleaner
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cleaner_name' => 'required|string|max:255',
            'cleaner_phoneNo' => 'required|string|max:15',
            'status' => 'required|in:available,unavailable',
        ]);

        Cleaner::create($validated);
        return redirect()->back()->with('success', 'Cleaner added successfully');
    }

    // Fetch available cleaners for API (used by AJAX)
    public function getAvailableCleaners(Request $request)
    {
        $limit = $request->get('limit', 10);
        $cleaners = Cleaner::where('status', 'available')->limit($limit)->get();

        return response()->json(['cleaners' => $cleaners]);
    }

    // Fetch all cleaners (general method)
    public function getCleaners()
    {
        $cleaners = Cleaner::all();
        $totalCleaners = $cleaners->count();
        return view('supervisor.cleaners.index', compact('cleaners', 'totalCleaners'));
    }

    // Edit a cleaner (display form for editing)
    public function edit($id)
    {
        $cleaner = Cleaner::findOrFail($id);
        return view('admin.cleaners.edit', compact('cleaner'));
    }

    // Show cleaner details via API
    public function show($id)
    {
        $cleaner = Cleaner::find($id);
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }
        return response()->json($cleaner);
    }

    // Update cleaner information
    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20',
            'status' => 'required|in:available,unavailable',
            'password' => 'nullable|confirmed|min:8'
        ]);

        $cleaner = Cleaner::findOrFail($id);

        // Update the cleaner information
        $cleaner->cleaner_username = $request->username;
        $cleaner->cleaner_name = $request->name;
        $cleaner->cleaner_phoneNo = $request->phone_no;
        $cleaner->status = $request->status;

        // Update password if provided
        if ($request->filled('password')) {
            $cleaner->cleaner_password = bcrypt($request->password);
        }

        // Handle profile picture upload if present
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $path = $file->store('profile_pics', 'public');
            $cleaner->profile_pic = $path;
        }

        // Save changes to the database
        $cleaner->save();

        return redirect()->route('admin.cleaners.edit', $cleaner->id)
                         ->with('success', 'Cleaner details updated successfully');
    }

    // Delete a cleaner
    public function destroy($id)
    {
        $cleaner = Cleaner::find($id);
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }

        $cleaner->delete();
        return response()->json(['message' => 'Cleaner deleted successfully'], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CleanerController extends Controller
{
    // Display all cleaners with additional counts
    public function index()
    {
        Log::info('Cleaners index accessed');
        
        $cleaners = Cleaner::all();
        
        // Count based on status
        $availableCount = $cleaners->where('status', 'available')->count();
        $unavailableCount = $cleaners->where('status', 'unavailable')->count();
        $totalCleaners = $cleaners->count();
        
        return view('supervisor.cleaners.index', compact('cleaners', 'totalCleaners', 'availableCount', 'unavailableCount'));
    }

    // Search and display paginated cleaners
    public function cleaners(Request $request)
    {
        $search = $request->input('search');
        
        $cleaners = Cleaner::when($search, function ($query, $search) {
                return $query->where('cleaner_name', 'LIKE', "%{$search}%");
            })
            ->paginate(10);
        
        // Count for available and unavailable cleaners
        $totalCleaners = Cleaner::count();
        $availableCount = Cleaner::where('status', 'available')->count();
        $unavailableCount = Cleaner::where('status', 'unavailable')->count();

        return view('supervisor.cleaners.index', compact('totalCleaners', 'availableCount', 'unavailableCount', 'cleaners'));
    }

    // Search cleaners via AJAX for autocomplete or dynamic search
    public function searchCleaners(Request $request)
    {
        $query = $request->query('q');
        
        $cleaners = Cleaner::when($query, function ($queryBuilder, $query) {
                return $queryBuilder->where('cleaner_name', 'like', '%' . $query . '%')
                    ->orWhere('cleaner_username', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get();

        return response()->json($cleaners);
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

    // Fetch available cleaners (for API or AJAX requests)
    public function getAvailableCleaners(Request $request)
    {
        $limit = $request->get('limit', 10);
        $cleaners = Cleaner::where('status', 'available')->limit($limit)->get();

        return response()->json(['cleaners' => $cleaners]);
    }

    // Show the form to edit cleaner information
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
        $cleaner = Cleaner::findOrFail($id);

        // Validate incoming request
        $validated = $request->validate([
            'cleaner_name' => 'sometimes|required|string|max:255',
            'cleaner_phoneNo' => 'sometimes|required|string|max:15',
            'status' => 'sometimes|required|in:available,unavailable',
            'username' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20',
            'password' => 'nullable|confirmed|min:8'
        ]);

        // Update cleaner attributes
        $cleaner->update([
            'cleaner_username' => $validated['username'] ?? $cleaner->cleaner_username,
            'cleaner_name' => $validated['cleaner_name'] ?? $cleaner->cleaner_name,
            'cleaner_phoneNo' => $validated['phone_no'] ?? $cleaner->cleaner_phoneNo,
            'status' => $validated['status'] ?? $cleaner->status,
            'cleaner_password' => $request->filled('password') ? bcrypt($validated['password']) : $cleaner->cleaner_password,
        ]);

        // Handle profile picture upload if present
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $path = $file->store('profile_pics', 'public');
            $cleaner->profile_pic = $path;
        }

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

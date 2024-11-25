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
    public function index(Request $request)
    {
        // Fetch cleaners with 'unavailable' status and assigned to ongoing complaints
        $cleaners = Cleaner::withOngoingComplaints()
                           ->paginate(10);

        $totalCleaners = Cleaner::count();
        $availableCount = Cleaner::where('status', 'available')->count();
        $unavailableCount = Cleaner::where('status', 'unavailable')->count();

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
        
        $totalCleaners = Cleaner::count();
        $availableCount = Cleaner::where('status', 'available')->count();
        $unavailableCount = Cleaner::where('status', 'unavailable')->count();

        return view('supervisor.cleaners.index', compact('totalCleaners', 'availableCount', 'unavailableCount', 'cleaners'));
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

}

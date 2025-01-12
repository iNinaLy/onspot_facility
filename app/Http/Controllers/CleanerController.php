<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
            ->paginate(5, ['*'], 'available_page');
        
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
    

    /**
     * Update the status of a cleaner.
     */
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
            'status' => 'required|in:available,unavailable,on_leave', // Added 'on_leave' if needed
        ]);

        // Update the status
        $cleaner->status = $validated['status'];
        $cleaner->save();

        // Redirect back with success message
        return redirect()->route('admin.cleaners')->with('success', 'Cleaner status updated successfully.');
    }
}
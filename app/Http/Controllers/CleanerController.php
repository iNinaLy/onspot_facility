<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CleanerController extends Controller
{
    public function index(Request $request)
    {
        // Basic search and sort inputs
        $search        = $request->input('search', '');
        $sortColumn    = $request->input('sort_column', 'cleaner_name');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Validate sort column and direction to avoid SQL injection or invalid columns
        if (! in_array($sortColumn, ['cleaner_name', 'building'])) {
            $sortColumn = 'cleaner_name';
        }
        if (! in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Retrieve summary counts
        $totalCleaners    = Cleaner::count();
        $availableCount   = Cleaner::where('status', 'available')->count();
        $unavailableCount = Cleaner::where('status', 'unavailable')->count();

        // Query builder for "available" cleaners
        $availableCleanersQuery = Cleaner::where('status', 'available')
            ->with(['complaints' => function ($q) {
                $q->where('comp_status', 'ongoing');
            }]);

        // If search is present, apply it
        if ($search) {
            $availableCleanersQuery->where(function ($query) use ($search) {
                $query->where('cleaner_name', 'like', "%{$search}%")
                      ->orWhere('building', 'like', "%{$search}%");
            });
        }

        // Order and paginate (using a custom page name to differentiate between tabs)
        $availableCleaners = $availableCleanersQuery
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(5, ['*'], 'available_page');

        // Query builder for "unavailable" cleaners
        $unavailableCleanersQuery = Cleaner::where('status', 'unavailable')
            ->with(['complaints' => function ($q) {
                $q->where('comp_status', 'ongoing');
            }]);

        if ($search) {
            $unavailableCleanersQuery->where(function ($query) use ($search) {
                $query->where('cleaner_name', 'like', "%{$search}%")
                      ->orWhere('building', 'like', "%{$search}%");
            });
        }

        $unavailableCleaners = $unavailableCleanersQuery
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(5, ['*'], 'unavailable_page');

        return view('supervisor.cleaners.index', compact(
            'totalCleaners',
            'availableCount',
            'unavailableCount',
            'availableCleaners',
            'unavailableCleaners',
            'search',
            'sortColumn',
            'sortDirection'
        ));
    }

    public function ajaxSearch(Request $request)
    {
        $query = $request->get('query', '');

        // Example: limit results to 10 to keep it fast
        $cleaners = Cleaner::when($query, function ($q) use ($query) {
                $q->where('cleaner_name', 'like', "%{$query}%")
                ->orWhere('building', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id','cleaner_name','cleaner_phoneNo','building','status']);

        // Return as JSON
        return response()->json($cleaners);
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

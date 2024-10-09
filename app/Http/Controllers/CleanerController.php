<?php
namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CleanerController extends Controller
{
    public function index()
    {
        Log::info('Cleaners index accessed');
    
        // Fetch all cleaners
        $cleaners = Cleaner::all();
    
        // Log the cleaners data
        Log::info('Cleaners data: ', $cleaners->toArray());
    
        // Count based on status
        $availableCount = $cleaners->where('status', 'available')->count();
        $unavailableCount = $cleaners->where('status', 'unavailable')->count();
        $totalCleaners = $cleaners->count();
    
        // Return the view with the necessary data
        return view('supervisor.cleaners.index', compact('cleaners', 'totalCleaners', 'availableCount', 'unavailableCount'));
    }

    public function show($id)
    {
        $cleaner = Cleaner::find($id);
        
        // Check if the cleaner exists
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }

        return $cleaner;
    }

    public function update(Request $request, $id)
    {
        $cleaner = Cleaner::find($id);
        
        // Check if the cleaner exists
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }

        // Validate incoming request
        $validated = $request->validate([
            'cleaner_name' => 'sometimes|required|string|max:255',
            'cleaner_phoneNo' => 'sometimes|required|string|max:15',
            'status' => 'sometimes|required|in:available,unavailable', // Use cleaner_available instead of status
        ]);

        // Update cleaner information
        $cleaner->update($validated);
        return $cleaner;
    }

    public function destroy($id)
    {
        $cleaner = Cleaner::find($id);
        
        // Check if the cleaner exists
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }

        // Delete the cleaner
        $cleaner->delete();
        return response()->json(['message' => 'Cleaner deleted'], 200);
    }
}

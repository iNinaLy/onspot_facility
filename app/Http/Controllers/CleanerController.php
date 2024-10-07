<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 

class CleanerController extends Controller
{
    public function index()
    {
        Log::info('Cleaners index accessed'); // Log this message
        $cleaners = Cleaner::all();
        $availableCount = $cleaners->where('status', 'available')->count();
        $unavailableCount = $cleaners->where('status', 'unavailable')->count();
        $totalCleaners = $cleaners->count();
        $totalCleaners = Cleaner::count();
        $availableCleanersCount = Cleaner::where('status', 'available')->count();
        return view('supervisor.cleaners.index', compact('cleaners', 'totalCleaners','availableCount', 'unavailableCount'));
       
    }
    

    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'cleaner_name' => 'required|string|max:255',
            'cleaner_phoneNo' => 'required|string|max:15',
            'status' => 'required|in:available,unavailable', // Use status instead of cleaner_available
        ]);

        // Create a new cleaner record
        return Cleaner::create($validated);
    }

    public function show($id)
    {
        $cleaner = Cleaner::find($id);
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }
        return $cleaner;
    }

    public function update(Request $request, $id)
    {
        $cleaner = Cleaner::find($id);
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }

        // Validate incoming request
        $validated = $request->validate([
            'cleaner_name' => 'sometimes|required|string|max:255',
            'cleaner_phoneNo' => 'sometimes|required|string|max:15',
            'status' => 'sometimes|required|in:available,unavailable', // Use status instead of cleaner_available
        ]);

        // Update cleaner information
        $cleaner->update($validated);
        return $cleaner;
    }

    public function destroy($id)
    {
        $cleaner = Cleaner::find($id);
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }

        // Delete the cleaner
        $cleaner->delete();
        return response()->json(['message' => 'Cleaner deleted'], 200);
    }
}

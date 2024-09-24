<?php

namespace App\Http\Controllers;

use App\Models\Cleaner;
use Illuminate\Http\Request;

class CleanerController extends Controller
{
    public function index()
    {
        return Cleaner::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cleaner_name' => 'required|string|max:255',
            'cleaner_phoneNo' => 'required|string|max:15',
            'cleaner_available' => 'required|boolean',
        ]);

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

        $validated = $request->validate([
            'cleaner_name' => 'sometimes|required|string|max:255',
            'cleaner_phoneNo' => 'sometimes|required|string|max:15',
            'cleaner_available' => 'sometimes|required|boolean',
        ]);

        $cleaner->update($validated);
        return $cleaner;
    }

    public function destroy($id)
    {
        $cleaner = Cleaner::find($id);
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }

        $cleaner->delete();
        return response()->json(['message' => 'Cleaner deleted'], 200);
    }
}

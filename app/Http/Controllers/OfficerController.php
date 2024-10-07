<?php

// app/Http/Controllers/OfficerController.php
namespace App\Http\Controllers;

use App\Models\Officer;
use Illuminate\Http\Request;

class OfficerController extends Controller
{
    // Get all officers
    public function index()
    {
        return Officer::all();
    }

    // Store a new officer
    public function store(Request $request)
    {
        $officer = Officer::create($request->all());
        return response()->json($officer, 201);
    }

    // Show a single officer
    public function show($id)
    {
        return Officer::findOrFail($id);
    }

    // Update an officer
    public function update(Request $request, $id)
    {
        $officer = Officer::findOrFail($id);
        $officer->update($request->all());
        return response()->json($officer, 200);
    }

    // Delete an officer
    public function destroy($id)
    {
        Officer::destroy($id);
        return response()->json(null, 204);
    }
}
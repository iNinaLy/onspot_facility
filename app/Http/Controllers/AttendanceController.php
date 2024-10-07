<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // Display a listing of attendance records
    public function index()
    {
        return Attendance::all();
    }

    // Store a new attendance record
    public function store(Request $request)
    {
        $attendance = Attendance::create($request->all());
        return response()->json($attendance, 201);
    }

    // Display a specific attendance record
    public function show($id)
    {
        return Attendance::findOrFail($id);
    }

    // Update an existing attendance record
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->all());
        return response()->json($attendance, 200);
    }

    // Delete an attendance record
    public function destroy($id)
    {
        // Find the record, but don't fail if not found
        $attendance = Attendance::find($id);
    
        // Check if the record exists
        if ($attendance) {
            // If found, delete the record
            $attendance->delete();
            // Return success response
            return response()->json(['message' => 'Attendance record deleted successfully'], 200);
        } else {
            // If not found, return custom error message
            return response()->json(['message' => 'Attendance record not found'], 404);
        }
    }
    
}
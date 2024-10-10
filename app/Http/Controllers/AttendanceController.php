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
    public function markAttendance(Request $request)
    {
        // Validate the request data
        $request->validate([
            'status' => 'required|string|in:present,absent',
            'cleaner_id' => 'required|exists:cleaners,id', // Ensure cleaner_id exists in the cleaners table
        ]);
    
        try {
            // Set attend_in to current time and attend_date to current date
            $attend_in = now()->format('H:i:s');
            $attend_date = now()->toDateString();
    
            // Create or update attendance record for the cleaner and current date
            $attendance = Attendance::updateOrCreate(
                [
                    'attend_date' => $attend_date,
                    'cleaner_id' => $request->cleaner_id
                ],
                [
                    'attend_in' => $attend_in,
                    'attend_status' => $request->status === 'present' ? 'in' : 'out',
                ]
            );
    
            return response()->json([
                'message' => 'Attendance marked successfully!',
                'attendance' => $attendance,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error marking attendance.'], 500);
        }
    }
    

    // Display a specific attendance record
    public function show($id)
    {
        return Attendance::findOrFail($id);
    }

    // Update an existing attendance record
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'attend_date' => 'sometimes|required|date',
            'attend_in' => 'sometimes|required|date_format:H:i:s',
            'attend_status' => 'sometimes|required|string|in:in,out',
            'cleaner_id' => 'sometimes|required|exists:cleaners,id',
        ]);

        $attendance = Attendance::findOrFail($id);
        
        // Update the attendance record only with the fields present in the request
        $attendance->update($request->only(['attend_date', 'attend_in', 'attend_status', 'cleaner_id']));
        
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

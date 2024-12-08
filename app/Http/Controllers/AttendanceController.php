<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Cleaner; // Import the Cleaner model
use App\Models\User; // Import the User model for cleaner name

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer', // User ID of the cleaner
            'status' => 'required|in:present,absent', // Attendance status
        ]);

        \Log::info('Attendance Data:', $data);

        // Retrieve the cleaner record using user_id
        $cleaner = Cleaner::where('user_id', $data['id'])->firstOrFail();

        // Check if attendance already exists for today
        $existingAttendance = Attendance::where('cleaner_id', $cleaner->user_id) // Use user_id here
            ->whereDate('attend_date', now()->toDateString())
            ->first();

        if ($existingAttendance) {
            $cleanerName = $cleaner->cleaner_name;
            return response()->json([
                'success' => false,
                'message' => 'Attendance already submitted for today.',
                'attendance' => $existingAttendance,
                'cleaner_name' => $cleanerName,
            ], 200);
        }

        // Create a new attendance record
        $attendance = Attendance::create([
            'cleaner_id' => $cleaner->user_id, // Use user_id
            'attend_status' => $data['status'],
            'attend_date' => now(),
            'attend_in' => now(),
        ]);

        // Update the cleaner's status in the cleaners table
        $updateResult = $cleaner->update([
            'status' => $data['status'] === 'present' ? 'available' : 'unavailable',
        ]);

        if (!$updateResult) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cleaner status.',
            ], 500);
        }

        $cleanerName = $cleaner->cleaner_name;

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
            'cleaner_name' => $cleanerName,
        ], 201);
    }

    public function index()
    {
        $attendances = Attendance::with('cleaner')->get(); // Include cleaner details using relationship

        // Add cleaner names to the response
        $attendancesWithNames = $attendances->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'cleaner_id' => $attendance->cleaner_id,
                'cleaner_name' => $attendance->cleaner->cleaner_name ?? 'N/A', // Include cleaner name
                'attend_date' => $attendance->attend_date,
                'attend_in' => $attendance->attend_in,
                'attend_status' => $attendance->attend_status,
            ];
        });

        return response()->json($attendancesWithNames, 200);
    }

    public function checkTodayAttendance(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer', // User ID of the cleaner
        ]);

        // Retrieve the cleaner record using user_id
        $cleaner = Cleaner::where('user_id', $data['id'])->firstOrFail();

        $todayAttendance = Attendance::where('cleaner_id', $cleaner->user_id) // Use user_id here
            ->whereDate('attend_date', now()->toDateString())
            ->first();

        $cleanerName = $cleaner->cleaner_name;

        return response()->json([
            'attended' => $todayAttendance ? true : false,
            'attendance' => $todayAttendance,
            'cleaner_name' => $cleanerName,
            'status' => $cleaner->status,
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User; // Import the User model for cleaner name

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'status' => 'required|in:present,absent',
        ]);
    
        \Log::info('Attendance Data:', $data);
    
        // Check if attendance already exists for today
        $existingAttendance = Attendance::where('cleaner_id', $data['id'])
            ->whereDate('attend_date', now()->toDateString())
            ->first();
    
        if ($existingAttendance) {
            $cleanerName = User::where('id', $data['id'])->value('name'); // Retrieve cleaner name
            return response()->json([
                'success' => false,
                'message' => 'Attendance already submitted for today.',
                'attendance' => $existingAttendance,
                'cleaner_name' => $cleanerName,
            ], 200); // Return existing attendance
        }
    
        // Create a new attendance record
        $attendance = Attendance::create([
            'cleaner_id' => $data['id'],
            'attend_status' => $data['status'],
            'attend_date' => now(),
            'attend_in' => now(),
        ]);

        $cleanerName = User::where('id', $data['id'])->value('name'); // Retrieve cleaner name
    
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
                'cleaner_name' => $attendance->cleaner->name ?? 'N/A', // Include cleaner name
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
            'id' => 'required|integer',
        ]);

        $todayAttendance = Attendance::where('cleaner_id', $data['id'])
            ->whereDate('attend_date', now()->toDateString())
            ->first();

        $cleanerName = User::where('id', $data['id'])->value('name'); // Retrieve cleaner name

        return response()->json([
            'attended' => $todayAttendance ? true : false,
            'attendance' => $todayAttendance,
            'cleaner_name' => $cleanerName,
        ], 200);
    }
}

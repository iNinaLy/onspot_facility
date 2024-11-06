<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'status' => 'required|in:present,absent',
        ]);
    
        // Log the incoming data
        \Log::info('Attendance Data:', $data);
    
        $attendance = Attendance::create([
            'cleaner_id' => $data['id'],
            'attend_status' => $data['status'],
            'attend_date' => now(),
            'attend_in' => now(),
        ]);
    
        return response()->json(['success' => true, 'attendance' => $attendance], 201);
    }

    public function index()
{
    $attendances = Attendance::all(); // Fetch all attendance records
    return response()->json($attendances, 200);
}

}  
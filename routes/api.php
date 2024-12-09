<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CleanerController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\SupervisorController; 
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCleanerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Models\ComplaintCleaner;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ComplaintCleanerController;
use Illuminate\Http\Request;



//CLEANER ROUTES
// Cleaner Task and Complaint Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cleaner/{cleaner_id}/tasks', [ComplaintCleanerController::class, 'getCleanerTasks']); // List all tasks for a specific cleaner
    Route::get('/complaints/{id}/details', [ComplaintCleanerController::class, 'getComplaintDetailsConditional']); // Get details of a specific complaint
    Route::post('/tasks/{complaint_id}/mark-unavailable', [ComplaintCleanerController::class, 'markCleanerUnavailable']);

});

// Cleaner Attendance Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']); // Retrieve attendance records
    Route::post('/attendance', [AttendanceController::class, 'store']); // Submit attendance
    Route::post('/attendance/check', [AttendanceController::class, 'checkTodayAttendance']); // Check today's attendance
});


// Authentication routes
Route::post('/flutterlogin', [AuthController::class, 'login']);
Route::post('/flutterregister', [AuthController::class, 'register']); 

//Sanctum-protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/flutterlogout', [AuthController::class, 'logout']);
    Route::post('/store-token', [AuthController::class, 'storeNotificationToken']);
});

//Forgot Password Route
Route::post('/forgot-password', [AuthController::class, 'sendResetCode']);
Route::post('/reset-password', [AuthController::class, 'verifyResetCode']);

// Profile Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile', [ProfileController::class, 'apiupdate']);
});


//OFFICER ROUTES
// Officer routes
Route::middleware('auth:sanctum')->group(function () {
    // Complaint routes
    Route::post('/complaints', [ComplaintController::class, 'apistore']);
    Route::get('/complaints-history', [ComplaintController::class, 'getOfficerComplaints']);
    Route::get('/complaints-recent', [ComplaintController::class, 'recentComplaint']); //not working
    Route::get('/complaints/{id}/details', [ComplaintCleanerController::class, 'getComplaintDetailsConditional']);
    Route::post('/complaints/{id}/complete', [ComplaintController::class, 'completeComplaint']); 
});

//SUPERVISOR ROUTES
//Notifications
Route::middleware('auth:api')->get('/notifications', [NotificationController::class, 'getNotifications']);

//Supervisor Search Cleaners
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/supervisor/cleaners', [SupervisorController::class, 'getAllCleaners']);
    Route::get('supervisor/cleaner/{id}', [SupervisorController::class, 'showapi']);
});
//Supervisor View Complaints, Assign Task and History
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/supervisor/complaints', [ComplaintController::class, 'getComplaints']);
    Route::get('/supervisor/assign-task/{id}', [ComplaintController::class, 'apigetComplaintDetails']);
    Route::post('supervisor/assign-task/{id}/assign', [ComplaintController::class, 'AssignTask']);
    Route::get('/supervisor/history', [ComplaintController::class, 'getHistory']);
    Route::get('/supervisor/history/{id}', [ComplaintController::class, 'getHistoryDetails']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});



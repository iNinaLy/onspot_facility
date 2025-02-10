<?php

use App\Services\SupabaseService;
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
use Illuminate\Support\Facades\Mail;


// Route to expose Supabase configuration
Route::middleware('auth:sanctum')->get('/supabase-config', function () {
    return response()->json([
        'supabase_url' => env('SUPABASE_URL'),
        'supabase_key' => env('SUPABASE_KEY'),
    ]);
});

//CLEANER ROUTES
// Cleaner Task and Complaint Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks/latest/{cleaner_id}', [ComplaintCleanerController::class, 'getLatestTask']);
    Route::get('/complaints/{id}/details', [ComplaintCleanerController::class, 'getComplaintDetailsConditional']); // Get details of a specific complaint
    Route::post('/tasks/{complaint_id}/mark-unavailable', [ComplaintCleanerController::class, 'markCleanerUnavailable']);
    Route::get('/tasks/{cleaner_id}', [ComplaintCleanerController::class, 'unnotifiedTasks']);
    Route::post('/tasks/acknowledge', [ComplaintCleanerController::class, 'acknowledgeTask']);
    Route::get('/tasks/history/{cleaner_id}', [ComplaintCleanerController::class, 'getHistoryTasks']);
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
//Forgot Password Route
Route::post('/forgot-password', [AuthController::class, 'sendResetCode']);
Route::post('/verify-reset-code', [AuthController::class, 'verifyResetCode']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']); 

//Sanctum-protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/flutterlogout', [AuthController::class, 'logout']);
    Route::post('/store-token', [AuthController::class, 'storeNotificationToken']);
});

// Profile Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile', [ProfileController::class, 'apiupdate']);
    Route::post('/profile/picture', [ProfileController::class, 'storeProfilePicture']); // Upload or update
    Route::delete('/profile/picture', [ProfileController::class, 'deleteProfilePicture']); // Delete
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

//Supervisor Search Cleaners
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/supervisor/cleaners', [SupervisorController::class, 'getAllCleaners']);
    Route::get('supervisor/cleaner/{id}', [SupervisorController::class, 'showapi']);
});
//Supervisor View Complaints, Assign Task and History
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/complaints/latest', [ComplaintController::class, 'fetchLatestComplaint']);
    Route::get('/supervisor/complaints', [ComplaintController::class, 'getComplaints']);
    Route::get('/supervisor/assign-task/{id}', [ComplaintController::class, 'apigetComplaintDetails']);
    Route::post('supervisor/assign-task/{id}/assign', [ComplaintController::class, 'AssignTask']);
    Route::get('/supervisor/history', [ComplaintController::class, 'getHistory']);
    Route::get('/supervisor/history/{id}', [ComplaintController::class, 'getHistoryDetails']);
});

// API route for fetching user names
Route::post('/user-names', [ComplaintController::class, 'getUserNames']);


Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::get('/test-email', function () {
    Mail::raw('This is a test email.', function ($message) {
        $message->to('your_email@example.com')->subject('Test Email');
    });
    return 'Email sent successfully!';
});


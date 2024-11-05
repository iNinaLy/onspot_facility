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
use Illuminate\Http\Request;


//Cleaner Attendance Routes
Route::get('/attendance', [AttendanceController::class, 'index']); // For retrieving attendance records
Route::post('/attendance', [AttendanceController::class, 'store']); // For storing attendance records


// Authentication Routes
Route::post('/flutterlogin', [AuthController::class, 'login']);
Route::post('/flutterregister', [AuthController::class, 'register']); 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/flutterlogout', [AuthController::class, 'logout']);
});

//Forgot Password Route
Route::post('/forgot-password', [AuthController::class, 'sendResetCode']);
Route::post('/reset-password', [AuthController::class, 'verifyResetCode']);

// Profile Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile', [ProfileController::class, 'update']);
});

//Supervisor Search Cleaners
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/supervisor/cleaners', [SupervisorController::class, 'getAllCleaners']);
    Route::get('supervisor/cleaner/{id}', [SupervisorController::class, 'showapi']);
});
//Supervisor VIew Complaints, Assign Task and History
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/supervisor/complaints', [ComplaintController::class, 'getComplaints']);
    Route::get('/supervisor/assign-task/{id}', [ComplaintController::class, 'apigetComplaintDetails']);
    Route::post('supervisor/assign-task/{id}/assign', [ComplaintController::class, 'AssignTask']);
    Route::get('/supervisor/history', [ComplaintController::class, 'getHistory']);
    Route::get('/supervisor/history/{id}', [ComplaintController::class, 'getHistoryDetails']);

});

// Officer Routes
Route::middleware('auth:sanctum')->group(function () {
    // Complaint routes
    Route::post('/complaints', [ComplaintController::class, 'apistore']);
    Route::get('/complaints-history', [ComplaintController::class, 'getOfficerComplaints']);
    Route::get('/complaints-recent', [ComplaintController::class, 'recentComplaint']); 
    Route::get('/complaints/{id}/details', [ComplaintController::class, 'getComplaintDetails']);
});

// Cleaner Task Routes
Route::middleware('auth:sanctum')->group(function () {
    // Task Routes
    Route::post('/cleaner/{id}/tasks', [ComplaintController::class, 'apistore']);
    Route::get('/tasks/complaint/{id}', [ComplaintController::class, 'getOfficerComplaints']);
    Route::get('/cleaner/{id}/tasks/latest', [ComplaintController::class, 'recentComplaint']); 
   // Complaint Routes
   Route::get('/complaints/{id}/details', [ComplaintController::class, 'recentComplaint']); 
});

//Test
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

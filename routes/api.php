<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComplaintCleanerController;
use App\Http\Controllers\SupervisorController;

// Cleaner Task and Complaint Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cleaner/{cleaner_id}/tasks', [ComplaintCleanerController::class, 'getCleanerTasks']); // List all tasks for a specific cleaner
    Route::get('/complaints/{id}/details', [ComplaintCleanerController::class, 'getComplaintDetailsConditional']); // Get details of a specific complaint
});

// Cleaner Attendance Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']); // For retrieving attendance records
    Route::post('/attendance', [AttendanceController::class, 'store']); // For storing attendance records
});

// Authentication routes
Route::post('/flutterlogin', [AuthController::class, 'login']);
Route::post('/flutterregister', [AuthController::class, 'register']); 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/flutterlogout', [AuthController::class, 'logout']);
});

// Profile Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile', [ProfileController::class, 'apiupdate']);
});

// Officer routes
Route::middleware('auth:sanctum')->group(function () {
    // Complaint routes
    Route::post('/complaints', [ComplaintController::class, 'apistore']);
    Route::get('/complaints-history', [ComplaintController::class, 'getOfficerComplaints']);
    Route::get('/complaints-recent', [ComplaintController::class, 'recentComplaint']); //not working
    Route::get('/complaints/{id}/details', [ComplaintCleanerController::class, 'getComplaintDetailsConditional']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
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

Route::middleware('auth:sanctum')->post('/store-token', [AuthController::class, 'storeNotificationToken']);




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
use Illuminate\Http\Request;


// Cleaner Attendance Routes
Route::get('/attendance', [AttendanceController::class, 'index']); // For retrieving attendance records
Route::post('/attendance', [AttendanceController::class, 'store']); // For storing attendance records

// Officer Routes
Route::get('/officers', [OfficerController::class, 'index']);
Route::post('/officers', [OfficerController::class, 'store']);
Route::get('/officers/{id}', [OfficerController::class, 'show']);
Route::put('/officers/{id}', [OfficerController::class, 'update']);
Route::delete('/officers/{id}', [OfficerController::class, 'destroy']);


// Authentication routes
Route::post('/flutterlogin', [AuthController::class, 'login']);
Route::post('/flutterregister', [AuthController::class, 'register']); 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/flutterlogout', [AuthController::class, 'logout']);
});

// User management routes
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/flutteruser', [UserController::class, 'update']);
});

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
//Supervisor Assign Task
Route::middleware('auth:sanctum')->group(function () {
    Route::get('supervisor/complaints', [ComplaintController::class, 'getComplaints']);
    Route::get('/supervisor/assign-task/{id}', [ComplaintController::class, 'getComplaintDetails']);
    Route::post('supervisor/assign-task/{id}/assign', [ComplaintController::class, 'AssignTask']);
    Route::get('/supervisor/history', [ComplaintController::class, 'getHistory']);
    Route::get('/supervisor/history/{id}', [ComplaintController::class, 'getHistoryDetails']);

});


Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

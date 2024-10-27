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


// Cleaner Routes
Route::get('/cleaners', [CleanerController::class, 'index']);
Route::post('/cleaners', [CleanerController::class, 'store']);
Route::get('/cleaners/{id}', [CleanerController::class, 'show']);
Route::put('/cleaners/{id}', [CleanerController::class, 'update']);
Route::delete('/cleaners/{id}', [CleanerController::class, 'destroy']);
//Cleaner Attendance
Route::get('/attendance', [AttendanceController::class, 'index']); // For retrieving attendance records
Route::post('/attendance', [AttendanceController::class, 'store']); // For storing attendance records

// Officer Routes
Route::get('/officers', [OfficerController::class, 'index']);
Route::post('/officers', [OfficerController::class, 'store']);
Route::get('/officers/{id}', [OfficerController::class, 'show']);
Route::put('/officers/{id}', [OfficerController::class, 'update']);
Route::delete('/officers/{id}', [OfficerController::class, 'destroy']);


//supervisor
Route::get('/supervisors', [SupervisorController::class, 'index']);
Route::post('/supervisors', [SupervisorController::class, 'store']);
Route::get('/supervisors/{id}', [SupervisorController::class, 'show']);
Route::put('/supervisors/{id}', [SupervisorController::class, 'update']);
Route::delete('/supervisors/{id}', [SupervisorController::class, 'destroy']);



//Task
Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks/{id}', [TaskController::class, 'show']);
Route::put('/tasks/{id}', [TaskController::class, 'update']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);


// TaskCleaner 
Route::get('/tasks/{id}/cleaners', [TaskCleanerController::class, 'showCleanersForTask']);
Route::post('/tasks/assign-cleaner', [TaskCleanerController::class, 'assignCleanerToTask']);
Route::post('/tasks/remove-cleaner', [TaskCleanerController::class, 'removeCleanerFromTask']);
Route::post('/tasks', [TaskController::class, 'store']);

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

//Supervisor App
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/supervisor/cleaners', [SupervisorController::class, 'getAllCleaners']);
    Route::get('supervisor/cleaner/{id}', [SupervisorController::class, 'showapi']);

});
//Supervisor Assign Task
Route::middleware('auth:sanctum')->group(function () {
    Route::get('supervisor/complaints', [ComplaintController::class, 'getComplaints']);
    Route::post('supervisor/assign-task/{id}', [ComplaintController::class, 'AssignTask']);
});


Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

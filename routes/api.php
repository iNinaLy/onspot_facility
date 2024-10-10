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

// Cleaner Routes
Route::get('/cleaners', [CleanerController::class, 'index']);
Route::post('/cleaners', [CleanerController::class, 'store']);
Route::get('/cleaners/{id}', [CleanerController::class, 'show']);
Route::put('/cleaners/{id}', [CleanerController::class, 'update']);
Route::delete('/cleaners/{id}', [CleanerController::class, 'destroy']);

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


//complaints
Route::get('/complaints', [ComplaintController::class, 'index']);
Route::post('/complaints', [ComplaintController::class, 'store']);
Route::get('/complaints/{id}', [ComplaintController::class, 'show']);
Route::put('/complaints/{id}', [ComplaintController::class, 'update']);
Route::delete('/complaints/{id}', [ComplaintController::class, 'destroy']);

//attendance
Route::get('/attendance', [AttendanceController::class, 'index']);
Route::get('/attendance/{id}', [AttendanceController::class, 'show']);
Route::put('/attendance/{id}', [AttendanceController::class, 'update']);
Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy']);


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


//authentication
Route::post('/login-cleaner', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//attendance
Route::post('/attendance', [AttendanceController::class, 'markAttendance']);


Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});
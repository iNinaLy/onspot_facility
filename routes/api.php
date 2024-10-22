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


Route::post('/flutterregister', [AuthController::class, 'register']);
Route::post('/flutterlogin', [AuthController::class, 'login']);
Route::post('/flutterregister', [AuthController::class, 'register']); 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/flutterlogout', [AuthController::class, 'logout']);
});

// User management routes
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/flutteruser', [UserController::class, 'update']);
});
//attendance
 Route::get('/attendance', [AttendanceController::class, 'index']); // For retrieving attendance records
 Route::post('/attendance', [AttendanceController::class, 'store']); // For storing attendance records


Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

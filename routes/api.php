<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CleanerController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\SupervisorController; 
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

// Authentication routes
Route::post('/flutterlogin', [AuthController::class, 'login']);
Route::post('/flutterregister', [AuthController::class, 'register']); 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/flutterlogout', [AuthController::class, 'logout']);
});

// Profile Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile', [ProfileController::class, 'update']);
});

// Officer routes
Route::middleware('auth:sanctum')->group(function () {
    //Complaint page
    Route::post('/complaints', [ComplaintController::class, 'apistore']);
    Route::get('/complaints-history', [ComplaintController::class, 'getOfficerComplaints']);
    Route::get('/complaints-recent', [ComplaintController::class, 'recentComplaint']);
});

//attendance
Route::post('/attendance', [AttendanceController::class, 'markAttendance']);


Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});


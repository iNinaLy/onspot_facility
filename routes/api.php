<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CleanerController;

Route::get('/cleaners', [CleanerController::class, 'index']);
Route::post('/cleaners', [CleanerController::class, 'store']);
Route::get('/cleaners/{id}', [CleanerController::class, 'show']);
Route::put('/cleaners/{id}', [CleanerController::class, 'update']);
Route::delete('/cleaners/{id}', [CleanerController::class, 'destroy']);



Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

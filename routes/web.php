<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\CleanerController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});


//Supervisor Routes
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');
    
    // Complaints routes
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('supervisor.complaints.index');
    Route::get('/complaints/{id}', [ComplaintController::class, 'show'])->name('supervisor.complaints.show');
    Route::get('/supervisor/complaints/{id}', [ComplaintController::class, 'show'])->name('supervisor.complaints.show');
    Route::get('/supervisor/complaints', [ComplaintController::class, 'index'])->name('supervisor.complaints.index');
     // GET route to display the assign cleaner form
    Route::get('/supervisor/complaints/{id}/assign-cleaner', [ComplaintController::class, 'showAssignCleanerForm'])->name('assign.cleaner.form');

    // POST route to assign cleaner (submit form)
    Route::post('/supervisor/complaints/{id}/assign-cleaner', [ComplaintController::class, 'assignCleaner'])->name('assign.cleaner');


    // Cleaners routes
    Route::get('/cleaners', [CleanerController::class, 'index'])->name('supervisor.cleaners');
    Route::get('/cleaners/{id}', [CleanerController::class, 'show'])->name('supervisor.cleaners.show');
    Route::post('/cleaners/{id}/status', [CleanerController::class, 'updateStatus'])->name('supervisor.cleaners.updateStatus');
    Route::post('/complaints/{id}/assign-cleaner', [ComplaintController::class, 'assignCleaner'])->name('assign.cleaner');

    // History
    Route::get('/history', [SupervisorController::class, 'history'])->name('supervisor.history');
});



// Optional: If you need a public route to access all cleaners (not under supervisor's control)
Route::get('/cleaners', [CleanerController::class, 'index'])->name('cleaners');

// Cleaner
Route::get('/cleaner/my-tasks', [CleanerController::class, 'myTasks'])->name('cleaner.tasks');

// History
Route::get('/history', [HistoryController::class, 'index'])->name('history');

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\CleanerController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CleanersController;
use App\Http\Controllers\Admin\OfficersController;
use App\Http\Controllers\Admin\SupervisorsController;
use App\Http\Controllers\Admin\ComplaintsController;
use App\Http\Controllers\Admin\AdminHistoryController;

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

//Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('cleaners', AdminCleanerController::class)->names('admin.cleaners');
    Route::resource('officers', AdminOfficerController::class)->names('admin.officers');
    Route::resource('supervisors', AdminSupervisorController::class)->names('admin.supervisors');
    Route::resource('complaints', AdminComplaintController::class)->names('admin.complaints');
});


// Supervisor Routes
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');
    Route::get('/cleaners', [SupervisorController::class, 'cleaners'])->name('supervisor.cleaners');
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('supervisor.complaints.index');
    Route::get('/complaints/{id}', [ComplaintController::class, 'show'])->name('supervisor.complaints.show');
    Route::get('/history', [SupervisorController::class, 'history'])->name('supervisor.history');
});

// Optional: If you need a public route to access all cleaners (not under supervisor's control) 
Route::get('/cleaners', [CleanerController::class, 'index'])->name('cleaners');

// Cleaner
Route::get('/cleaner/my-tasks', [CleanerController::class, 'myTasks'])->name('cleaner.tasks');

// History
Route::get('/history', [HistoryController::class, 'index'])->name('history');

require __DIR__.'/auth.php';

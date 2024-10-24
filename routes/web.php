<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\CleanerController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;



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
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Cleaners
    Route::get('/cleaners', [AdminController::class, 'cleaners'])->name('admin.cleaners');
    Route::get('/cleaners/create', [AdminController::class, 'createCleaner'])->name('admin.cleaners.create');
    Route::post('/cleaners', [AdminController::class, 'storeCleaner'])->name('admin.cleaners.store');
    Route::get('/cleaners/{cleaner}', [AdminController::class, 'showCleaner'])->name('admin.cleaners.show');
    Route::get('/cleaners/{cleaner}/edit', [AdminController::class, 'editCleaner'])->name('admin.cleaners.edit');
    Route::put('/cleaners/{cleaner}', [AdminController::class, 'updateCleaner'])->name('admin.cleaners.update');
    Route::delete('/cleaners/{cleaner}', [AdminController::class, 'destroyCleaner'])->name('admin.cleaners.destroy');

    // Officers
    Route::get('/officers', [AdminController::class, 'officers'])->name('admin.officers');
    Route::get('/officers/search', [AdminController::class, 'searchOfficers']);
    Route::get('/officers/create', [AdminController::class, 'createOfficer'])->name('admin.officers.create');
    Route::post('/officers', [AdminController::class, 'storeOfficer'])->name('admin.officers.store');
    Route::get('/officers/{officer}/edit', [AdminController::class, 'editOfficer'])->name('admin.officers.edit');
    Route::put('/officers/{officer}', [AdminController::class, 'updateOfficer'])->name('admin.officers.update');
    Route::delete('/officers/{officer}', [AdminController::class, 'destroyOfficer'])->name('admin.officers.destroy');
    Route::get('/officers/{officer}', [AdminController::class, 'showOfficer'])->name('admin.officers.show');

    // Supervisors Routes
    Route::get('/supervisors', [AdminController::class, 'supervisors'])->name('admin.supervisors.index');
    Route::get('/supervisors/create', [AdminController::class, 'createSupervisor'])->name('admin.supervisors.create');
    Route::post('/supervisors', [AdminController::class, 'storeSupervisor'])->name('admin.supervisors.store');
    Route::get('/supervisors/{supervisor}/edit', [AdminController::class, 'editSupervisor'])->name('admin.supervisors.edit');
    Route::put('/supervisors/{supervisor}', [AdminController::class, 'updateSupervisor'])->name('admin.supervisors.update');
    Route::delete('/supervisors/{supervisor}', [AdminController::class, 'destroySupervisor'])->name('admin.supervisors.destroy');


    // Users
    Route::get('/users', [UserController::class, 'create'])->name('admin.users.create');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');

    // Complaints
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('admin.complaints');
    Route::post('admin/complaints/batch-update', [ComplaintController::class, 'batchUpdate'])->name('admin.complaints.batchUpdate');
    Route::get('/complaints/search', [AdminController::class, 'searchComplaints'])->name('admin.complaints.search'); // Search route
    Route::get('/complaints/{complaint}/edit', [AdminController::class, 'editComplaint'])->name('admin.complaints.edit');
    Route::put('/complaints/{complaint}', [AdminController::class, 'updateComplaint'])->name('admin.complaints.update');
    Route::delete('/complaints/{complaint}', [AdminController::class, 'destroyComplaint'])->name('admin.complaints.destroy');
});

// Supervisor Routes
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');
    
    // Route for showing cleaner management or list page
    Route::get('/cleaners', [SupervisorController::class, 'cleaners'])->name('supervisor.cleaners'); // Changed here

    // API route for fetching available cleaners (used by AJAX requests)
    Route::get('/api/cleaners', [CleanerController::class, 'getAvailableCleaners']);
    Route::get('/api/cleaners', [CleanerController::class, 'searchCleaners']);

    // Routes related to complaints
    Route::get('/complaints', [ComplaintController::class, 'supervisorIndex'])->name('supervisor.complaints.index');
    Route::get('/complaints/{id}', [ComplaintController::class, 'show'])->name('supervisor.complaints.show');
    Route::post('/complaints/{id}/assign-cleaner', [ComplaintController::class, 'assignCleaner'])->name('assign.cleaner');
    Route::post('/complaints/{id}/update-assignment', [ComplaintController::class, 'updateAssignment'])->name('complaints.updateAssignment');

    // History page
    Route::get('/history', [SupervisorController::class, 'history'])->name('supervisor.history');
});




// Optional: If you need a public route to access all cleaners (not under supervisor's control) 
Route::get('/cleaners', [CleanerController::class, 'index'])->name('cleaners');

// Cleaner
Route::get('/cleaner/my-tasks', [CleanerController::class, 'myTasks'])->name('cleaner.tasks');

// History
Route::get('/history', [HistoryController::class, 'index'])->name('history');

require __DIR__.'/auth.php';

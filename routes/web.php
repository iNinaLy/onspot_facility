<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\CleanerController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationTokenController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Profile routes (shared among all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Cleaners
    Route::get('/cleaners', [AdminController::class, 'cleaners'])->name('cleaners');
    Route::get('/cleaners/create', [AdminController::class, 'createCleaner'])->name('cleaners.create');
    Route::post('/cleaners', [AdminController::class, 'storeCleaner'])->name('cleaners.store');
    Route::get('/cleaners/{cleaner}', [AdminController::class, 'showCleaner'])->name('cleaners.show');
    Route::get('/cleaners/{cleaner}/edit', [AdminController::class, 'editCleaner'])->name('cleaners.edit');
    Route::put('/cleaners/{cleaner}', [AdminController::class, 'updateCleaner'])->name('cleaners.update');
    Route::delete('/cleaners/{cleaner}', [AdminController::class, 'destroyCleaner'])->name('cleaners.destroy');

    // Cleaner-specific actions
    Route::patch('/cleaners/{id}/update-status', [CleanerController::class, 'updateStatus'])->name('cleaners.updateStatus');
    Route::patch('/cleaners/{id}/reset-password', [CleanerController::class, 'resetPassword'])->name('cleaners.resetPassword');
    
    // Officers
    Route::get('/officers', [AdminController::class, 'officers'])->name('officers');
    Route::get('/officers/search', [AdminController::class, 'searchOfficers']);
    Route::get('/officers/create', [AdminController::class, 'createOfficer'])->name('officers.create');
    Route::post('/officers', [AdminController::class, 'storeOfficer'])->name('officers.store');
    Route::get('/officers/{officer}/edit', [AdminController::class, 'editOfficer'])->name('officers.edit');
    Route::put('/officers/{officer}', [AdminController::class, 'updateOfficer'])->name('officers.update');
    Route::delete('/officers/{officer}', [AdminController::class, 'destroyOfficer'])->name('officers.destroy');
    Route::get('/officers/{officer}', [AdminController::class, 'showOfficer'])->name('officers.show');
    Route::patch('/officers/{id}/reset-password', [AdminController::class, 'resetOfficerPassword'])->name('officers.resetPassword');

    // Supervisors
    Route::get('/supervisors', [AdminController::class, 'supervisors'])->name('supervisors.index');
    Route::get('/supervisors/create', [AdminController::class, 'createSupervisor'])->name('supervisors.create');
    Route::post('/supervisors', [AdminController::class, 'storeSupervisor'])->name('supervisors.store');
    Route::get('/supervisors/{supervisor}/edit', [AdminController::class, 'editSupervisor'])->name('supervisors.edit');
    Route::put('/supervisors/{supervisor}', [AdminController::class, 'updateSupervisor'])->name('supervisors.update');
    Route::delete('/supervisors/{supervisor}', [AdminController::class, 'destroySupervisor'])->name('supervisors.destroy');
    Route::patch('/supervisors/{id}/reset-password', [AdminController::class, 'resetSupervisorPassword'])->name('supervisors.resetPassword');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index'); // List users
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create'); // Show the form
    Route::post('/users', [UserController::class, 'store'])->name('users.store'); // Handle form submission
   
    // Complaints
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints');
    Route::post('/complaints/batch-update', [AdminController::class, 'batchUpdate'])->name('complaints.batchUpdate');
    Route::get('/complaints/search', [AdminController::class, 'searchComplaints'])->name('complaints.search');
    Route::put('/complaints/{id}/status', [AdminController::class, 'updateStatus'])->name('complaints.updateStatus');
    Route::get('/complaints/{complaint}/edit', [AdminController::class, 'editComplaint'])->name('complaints.edit');
    Route::delete('/complaints/{complaint}', [AdminController::class, 'destroyComplaint'])->name('complaints.destroy');

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Supervisor Routes
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/supervisor/notifications/remove-read', [NotificationController::class, 'removeReadNotifications'])
    ->name('supervisor.notifications.removeRead');

    // Cleaner management
    Route::get('/cleaners', [SupervisorController::class, 'cleaners'])->name('cleaners');
    Route::get('/pending-complaints', [ComplaintController::class, 'getPendingComplaints']);
    Route::post('/assign-cleaner', [ComplaintController::class, 'assignCleanerToComplaint']);
  
    Route::get('/complaints/pending', [ComplaintController::class, 'getPendingComplaints']);
    Route::post('/complaints/assign', [ComplaintController::class, 'assignCleaner']);

    // API routes for fetching available cleaners
    Route::get('/api/cleaners', [CleanerController::class, 'getAvailableCleaners']);
    Route::get('/api/cleaners/search', [CleanerController::class, 'searchCleaners']);

    // Complaint routes
    Route::get('/complaints', [ComplaintController::class, 'supervisorIndex'])->name('complaints.index');
    Route::get('/complaints/{id}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{id}/assign-cleaner', [ComplaintController::class, 'assignCleaner'])->name('assign.cleaner');
    Route::post('/complaints/{id}/update-assignment', [ComplaintController::class, 'updateAssignment'])->name('complaints.updateAssignment');
   
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/update-device-token', [NotificationTokenController::class, 'updateToken']);
    });
    
    // History page
    Route::get('/history', [SupervisorController::class, 'history'])->name('history');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// General routes
Route::get('/cleaners', [CleanerController::class, 'index'])->name('cleaners'); // Public route
Route::get('/cleaner/my-tasks', [CleanerController::class, 'myTasks'])->name('cleaner.tasks'); // Cleaner-specific route
Route::get('/history', [HistoryController::class, 'index'])->name('history'); // History route

// Auth routes
require __DIR__.'/auth.php';

// Test image route
Route::get('/test-image', function () {
    return view('test_image');
});

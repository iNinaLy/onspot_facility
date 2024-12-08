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

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// General Profile Routes (for any authenticated user)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===================
// Admin Routes
// ===================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Cleaners Management
    Route::get('/cleaners', [AdminController::class, 'cleaners'])->name('cleaners');
    Route::get('/cleaners/create', [AdminController::class, 'createCleaner'])->name('cleaners.create');
    Route::post('/cleaners', [AdminController::class, 'storeCleaner'])->name('cleaners.store');
    Route::get('/cleaners/{cleaner}', [AdminController::class, 'showCleaner'])->name('cleaners.show');
    Route::get('/cleaners/{cleaner}/edit', [AdminController::class, 'editCleaner'])->name('cleaners.edit');
    Route::put('/cleaners/{cleaner}', [AdminController::class, 'updateCleaner'])->name('cleaners.update');
    Route::delete('/cleaners/{cleaner}', [AdminController::class, 'destroyCleaner'])->name('cleaners.destroy');
    Route::patch('/cleaners/{id}/update-status', [CleanerController::class, 'updateStatus'])->name('cleaners.updateStatus');
    Route::patch('/cleaners/{id}/reset-password', [CleanerController::class, 'resetPassword'])->name('cleaners.resetPassword');

    // Officers Management
    Route::get('/officers', [AdminController::class, 'officers'])->name('officers');
    Route::get('/officers/create', [AdminController::class, 'createOfficer'])->name('officers.create');
    Route::post('/officers', [AdminController::class, 'storeOfficer'])->name('officers.store');
    Route::get('/officers/{officer}/edit', [AdminController::class, 'editOfficer'])->name('officers.edit');
    Route::put('/officers/{officer}', [AdminController::class, 'updateOfficer'])->name('officers.update');
    Route::delete('/officers/{officer}', [AdminController::class, 'destroyOfficer'])->name('officers.destroy');
    Route::patch('/officers/{id}/reset-password', [AdminController::class, 'resetOfficerPassword'])->name('officers.resetPassword');

    // Supervisors Management
    Route::get('/supervisors', [AdminController::class, 'supervisors'])->name('supervisors.index');
    Route::get('/supervisors/create', [AdminController::class, 'createSupervisor'])->name('supervisors.create');
    Route::post('/supervisors', [AdminController::class, 'storeSupervisor'])->name('supervisors.store');
    Route::get('/supervisors/{supervisor}/edit', [AdminController::class, 'editSupervisor'])->name('supervisors.edit');
    Route::put('/supervisors/{supervisor}', [AdminController::class, 'updateSupervisor'])->name('supervisors.update');
    Route::delete('/supervisors/{supervisor}', [AdminController::class, 'destroySupervisor'])->name('supervisors.destroy');
    Route::patch('/supervisors/{id}/reset-password', [AdminController::class, 'resetSupervisorPassword'])->name('supervisors.resetPassword');

    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    /// Complaints Management
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints');
    Route::post('/complaints/bulk-action', [ComplaintController::class, 'bulkAction'])->name('complaints.bulkAction');
    Route::post('/complaints/{id}/inline-update', [ComplaintController::class, 'inlineUpdate'])->name('complaints.inlineUpdate');
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'submitComplaint'])->name('complaints.submit');
    Route::get('/complaints/{id}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/complaints/dashboard', [ComplaintController::class, 'showDashboard'])->name('complaints.dashboard');
    Route::put('/complaints/{id}', [ComplaintController::class, 'update'])->name('complaints.update');
    Route::delete('/complaints/{id}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');
    Route::post('/complaints/{id}/assign-cleaner', [ComplaintController::class, 'assignCleaner'])->name('complaints.assignCleaner');
    
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [AdminController::class, 'editProfile'])->name('edit');
        Route::patch('/', [AdminController::class, 'updateProfile'])->name('update');
        Route::delete('/', [AdminController::class, 'destroyProfile'])->name('destroy');
    });
});

// ===================
// Supervisor Routes
// ===================

Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');

   
        // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        // Display notifications index
        Route::get('/', [NotificationController::class, 'index'])->name('index');

        // Fetch all notifications via AJAX
        Route::get('/all', [NotificationController::class, 'fetchAll'])->name('all');

        // Mark a specific notification as read
        Route::post('/read/{id}', [NotificationController::class, 'markAsRead'])->name('markAsRead');

        // Mark all notifications as read
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        
        //Delete
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Clear all notifications
        Route::post('/clear-all', [NotificationController::class, 'clearAll'])->name('clearAll');
    });

    // Cleaner Management
    Route::prefix('cleaners')->group(function () {
        Route::get('/', [SupervisorController::class, 'cleaners'])->name('cleaners');
        Route::get('/api', [CleanerController::class, 'getAvailableCleaners'])->name('api.available');
        Route::get('/api/search', [CleanerController::class, 'searchCleaners'])->name('api.search');
    });

    // Complaints
    Route::prefix('complaints')->name('complaints.')->group(function () {
        Route::get('/', [ComplaintController::class, 'supervisorIndex'])->name('index');
        Route::get('/pending', [ComplaintController::class, 'getPendingComplaints'])->name('pending');
        Route::get('/assigned', [ComplaintController::class, 'getAssignedComplaints'])->name('assigned');
        Route::get('/ongoing', [ComplaintController::class, 'getOngoingComplaints'])->name('ongoing');
        Route::get('/completed', [ComplaintController::class, 'getCompletedComplaints'])->name('completed');
        Route::get('/{id}', [ComplaintController::class, 'show'])->name('show');
        Route::post('/submit', [ComplaintController::class, 'submitComplaint'])->name('submit');
        Route::post('/{id}/assign-cleaner', [ComplaintController::class, 'assignCleaner'])->name('assign-cleaner');
        Route::patch('/{id}/status', [ComplaintController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{id}', [ComplaintController::class, 'destroy'])->name('destroy');
    });

    // History
    Route::get('/history', [SupervisorController::class, 'history'])->name('history');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [SupervisorController::class, 'editProfile'])->name('edit');
        Route::patch('/', [SupervisorController::class, 'updateProfile'])->name('update');
        Route::delete('/', [SupervisorController::class, 'destroyProfile'])->name('destroy');
    });
});


// ===================
// General Public Routes
// ===================
Route::get('/cleaners', [CleanerController::class, 'index'])->name('cleaners'); // Public cleaner route
Route::get('/cleaner/my-tasks', [CleanerController::class, 'myTasks'])->name('cleaner.tasks');
Route::get('/history', [HistoryController::class, 'index'])->name('history'); // General history

// Authentication Routes
require __DIR__ . '/auth.php';

// Test Image Route
Route::get('/test-image', function () {
    return view('test_image');
});

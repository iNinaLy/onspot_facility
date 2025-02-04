<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\CleanerController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationTokenController;
use App\Models\Supervisor;

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
    Route::get('/cleaners', [CleanerController::class, 'cleaners'])->name('cleaners');
    Route::get('/cleaners/create', [CleanerController::class, 'createCleaner'])->name('cleaners.create');
    Route::post('/cleaners', [CleanerController::class, 'storeCleaner'])->name('cleaners.store');
    Route::get('/cleaners/{cleaner}', [CleanerController::class, 'showCleaner'])->name('cleaners.show');
    Route::put('/cleaners/{cleaner}', [CleanerController::class, 'updateCleaner'])->name('cleaners.update');
    Route::delete('/cleaners/{cleaner}', [CleanerController::class, 'destroyCleaner'])->name('cleaners.destroy');
    Route::post('/cleaners/{id}/inline-update', [CleanerController::class, 'inlineUpdate'])->name('cleaners.inlineUpdate');
    Route::patch('/cleaners/{id}/update-status', [CleanerController::class, 'updateStatus'])->name('cleaners.updateStatus');
    Route::patch('/cleaners/{id}/reset-password', [CleanerController::class, 'resetPassword'])->name('cleaners.resetPassword');

    // Officers Management
    Route::get('/officers', [OfficerController::class, 'index'])->name('officers');
    Route::get('/officers/create', [OfficerController::class, 'create'])->name('officers.create');
    Route::post('/officers', [OfficerController::class, 'store'])->name('officers.store');
    Route::get('/officers/{officer}/edit', [OfficerController::class, 'edit'])->name('officers.edit');
    Route::put('/officers/{officer}', [OfficerController::class, 'update'])->name('officers.update');
    Route::delete('/officers/{officer}', [OfficerController::class, 'destroy'])->name('officers.destroy');
    Route::patch('/officers/{id}/reset-password', [OfficerController::class, 'resetPassword'])->name('officers.resetPassword');

    // Supervisors Management
    Route::get('/supervisors', [SupervisorController::class, 'supervisors'])->name('supervisors.index');
    Route::get('/supervisors/create', [SupervisorController::class, 'createSupervisor'])->name('supervisors.create');
    Route::post('/supervisors', [SupervisorController::class, 'storeSupervisor'])->name('supervisors.store');
    Route::get('/supervisors/{supervisor}/edit', [SupervisorController::class, 'editSupervisor'])->name('supervisors.edit');
    Route::put('/supervisors/{supervisor}', [SupervisorController::class, 'updateSupervisor'])->name('supervisors.update');
    Route::delete('/supervisors/{supervisor}', [SupervisorController::class, 'destroySupervisor'])->name('supervisors.destroy');
    Route::patch('/supervisors/{id}/reset-password', [SupervisorController::class, 'resetSupervisorPassword'])->name('supervisors.resetPassword');

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
        Route::put('/', [AdminController::class, 'updateProfile'])->name('update');
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
        
        // Fetch all notifications via AJAX (if you have that functionality)
        Route::get('/all', [NotificationController::class, 'fetchAll'])->name('all');
        
        // Mark a specific notification as read
        Route::post('/read/{id}', [NotificationController::class, 'markAsRead'])->name('markAsRead');

        // Mark all notifications as read
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        
        // Delete a specific notification
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');

        // Clear all notifications
        Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clearAll');

        Route::get('/redirect/{notificationId}', [NotificationController::class, 'redirectToComplaint'])
            ->name('redirectToComplaint');
    });

    // Cleaner Management
    Route::prefix('cleaners')->group(function () {
        Route::get('/', [CleanerController::class, 'index'])->name('cleaners');
        Route::get('/api', [CleanerController::class, 'getAvailableCleaners'])->name('api.available');
        Route::get('/api/search', [CleanerController::class, 'searchCleaners'])->name('api.search');
        Route::get('/cleaners/ajax-search', [CleanerController::class, 'ajaxSearch'])->name('cleaners.ajaxSearch');
        
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

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [SupervisorController::class, 'editProfile'])->name('edit');
        Route::put('/', [SupervisorController::class, 'updateProfile'])->name('update');
        Route::delete('/', [SupervisorController::class, 'destroyProfile'])->name('destroy');
    });
});


// Authentication Routes
require __DIR__ . '/auth.php';


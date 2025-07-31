<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Storage;

// Default route - redirect to admin login
Route::redirect('/', '/admin/login');

// Public routes (accessible without authentication)
Route::middleware(['web'])->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
});

// Protected admin routes requiring authentication
Route::middleware(['web', 'auth:admin'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Appointment routes
    Route::get('/appointment', [AppointmentController::class, 'index'])->name('appointment.index');
    Route::get('/appointment/{id}/edit', [AppointmentController::class, 'edit'])->name('appointment.edit');
    Route::put('/appointment/{id}', [AppointmentController::class, 'update'])->name('appointment.update');
    Route::delete('/appointment/{id}', [AppointmentController::class, 'destroy'])->name('appointment.destroy');

    // User management routes
    Route::get('/user_management', [UserManagementController::class, 'index'])->name('user.management');
    Route::get('/user_management/create', [UserManagementController::class, 'create'])->name('user.create');
    Route::post('/user_management', [UserManagementController::class, 'store'])->name('user.store');
    Route::get('/user_management/{id}/edit', [UserManagementController::class, 'edit'])->name('user.edit');
    Route::put('/user_management/{id}', [UserManagementController::class, 'update'])->name('user.update');
    Route::delete('/user_management/{id}', [UserManagementController::class, 'destroy'])->name('user.destroy');

    // Hospital routes
    Route::get('/hospital', [HospitalController::class, 'index'])->name('hospital.index');
    Route::get('/hospital/create', [HospitalController::class, 'create'])->name('hospital.create');
    Route::post('/hospital', [HospitalController::class, 'store'])->name('hospital.store');
    Route::get('/hospital/{id}/edit', [HospitalController::class, 'edit'])->name('hospital.edit');
    Route::put('/hospital/{id}', [HospitalController::class, 'update'])->name('hospital.update');
    Route::delete('/hospital/{id}', [HospitalController::class, 'destroy'])->name('hospital.destroy');

    // Feedback and subscription routes
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/sub', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/{id}/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
    Route::put('/subscription/{id}', [SubscriptionController::class, 'update'])->name('subscription.update');
    Route::delete('/subscription/{id}', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
    
    // Logout route
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Profile routes
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

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

// Default route - redirect to admin login

// Admin 
    Route::get('/', [DashboardController::class, 'index']);
    
    Route::get('/appointment', [AppointmentController::class, 'index'])->name('appointment.index');
    Route::get('/appointment/{id}/edit', [AppointmentController::class, 'edit'])->name('appointment.edit');
    Route::put('/appointment/{id}', [AppointmentController::class, 'update'])->name('appointment.update');
    Route::delete('/appointment/{id}', [AppointmentController::class, 'destroy'])->name('appointment.destroy');

    Route::get('/user_management', [UserManagementController::class, 'index'])->name('user.management');
    Route::get('/user_management/{id}/edit', [UserManagementController::class, 'edit'])->name('user.edit');
    Route::put('/user_management/{id}', [UserManagementController::class, 'update'])->name('user.update');
    Route::delete('/user_management/{id}', [UserManagementController::class, 'destroy'])->name('user.destroy');

    Route::get('/hospital', [HospitalController::class, 'index'])->name('hospital.index');
    Route::get('/hospital/{id}/edit', [HospitalController::class, 'edit'])->name('hospital.edit');
    Route::put('/hospital/{id}', [HospitalController::class, 'update'])->name('hospital.update');
    Route::delete('/hospital/{id}', [HospitalController::class, 'destroy'])->name('hospital.destroy');

    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/sub', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/{id}/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
    Route::put('/subscription/{id}', [SubscriptionController::class, 'update'])->name('subscription.update');
    Route::delete('/subscription/{id}', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
    
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

   
    
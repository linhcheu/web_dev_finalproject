<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
});

Route::middleware('auth:admin')->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/appointment', [AppointmentController::class, 'index'])->name('appointment.index');
    Route::get('/appointment/{id}/edit', [AppointmentController::class, 'edit'])->whereNumber('id')->name('appointment.edit');
    Route::put('/appointment/{id}', [AppointmentController::class, 'update'])->whereNumber('id')->name('appointment.update');
    Route::delete('/appointment/{id}', [AppointmentController::class, 'destroy'])->whereNumber('id')->name('appointment.destroy');

    Route::get('/user_management', [UserManagementController::class, 'index'])->name('user.management');
    Route::get('/user_management/create', [UserManagementController::class, 'create'])->name('user.create');
    Route::post('/user_management', [UserManagementController::class, 'store'])->name('user.store');
    Route::get('/user_management/{id}/edit', [UserManagementController::class, 'edit'])->whereNumber('id')->name('user.edit');
    Route::put('/user_management/{id}', [UserManagementController::class, 'update'])->whereNumber('id')->name('user.update');
    Route::delete('/user_management/{id}', [UserManagementController::class, 'destroy'])->whereNumber('id')->name('user.destroy');

    Route::get('/hospital', [HospitalController::class, 'index'])->name('hospital.index');
    Route::get('/hospital/create', [HospitalController::class, 'create'])->name('hospital.create');
    Route::post('/hospital', [HospitalController::class, 'store'])->name('hospital.store');
    Route::get('/hospital/{id}/edit', [HospitalController::class, 'edit'])->whereNumber('id')->name('hospital.edit');
    Route::put('/hospital/{id}', [HospitalController::class, 'update'])->whereNumber('id')->name('hospital.update');
    Route::delete('/hospital/{id}', [HospitalController::class, 'destroy'])->whereNumber('id')->name('hospital.destroy');

    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy'])->whereNumber('id')->name('feedback.destroy');

    Route::get('/sub', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/{id}/edit', [SubscriptionController::class, 'edit'])->whereNumber('id')->name('subscription.edit');
    Route::put('/subscription/{id}', [SubscriptionController::class, 'update'])->whereNumber('id')->name('subscription.update');
    Route::delete('/subscription/{id}', [SubscriptionController::class, 'destroy'])->whereNumber('id')->name('subscription.destroy');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

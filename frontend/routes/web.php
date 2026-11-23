<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ---- Public ----------------------------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/subscription-plans', 'subscription_view')->name('subscription_view');
Route::get('/hospitals', [HospitalController::class, 'index'])->name('hospitals.index');
Route::get('/hospitals/{hospital}', [HospitalController::class, 'show'])->whereNumber('hospital')->name('hospitals.show');

// ---- Guests ----------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/hospital-register', [AuthController::class, 'showHospitalRegister'])->name('hospital.register');
    Route::post('/hospital-register', [AuthController::class, 'hospitalRegister']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---- Patients --------------------------------------------------------------
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::redirect('/dashboard', '/appointments')->name('dashboard');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->whereNumber('appointment')->name('appointments.show');
    Route::get('/appointments/{appointment}/receipt', [AppointmentController::class, 'receipt'])->whereNumber('appointment')->name('appointments.receipt');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/feedback', [FeedbackController::class, 'index'])->name('user.feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

// ---- Hospital administrators ----------------------------------------------
Route::middleware(['auth', 'role:hospital_admin'])->prefix('hospital')->name('hospital.')->group(function () {
    Route::get('/dashboard', [HospitalController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments', [HospitalController::class, 'appointments'])->name('appointments');
    Route::put('/appointments/{appointment}', [HospitalController::class, 'updateAppointment'])->whereNumber('appointment')->name('appointments.update');
    Route::get('/profile', [HospitalController::class, 'profile'])->name('profile');
    Route::put('/profile', [HospitalController::class, 'updateProfile'])->name('profile.update');
    Route::get('/subscription', [HospitalController::class, 'subscription'])->name('subscription');
    Route::put('/subscription', [HospitalController::class, 'updateSubscription'])->name('subscription.update');
    Route::put('/subscription/cancel', [HospitalController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::get('/feedback', [FeedbackController::class, 'hospitalIndex'])->name('feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

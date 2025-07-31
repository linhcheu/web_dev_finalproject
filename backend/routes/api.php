<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are for the backend admin panel API functionality
| All routes return JSON responses
*/

// Test route to verify backend API is working
Route::get('/test', function() {
    return response()->json([
        'success' => true,
        'message' => 'Backend API is working!',
        'timestamp' => now(),
        'service' => 'CareConnect Backend API'
    ]);
});

// Admin Authentication Routes
Route::prefix('auth')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm']);
    Route::post('/login', [AdminAuthController::class, 'login']);
});

// Protected Admin Routes - Require Authentication
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Authentication
    Route::post('/auth/logout', [AdminAuthController::class, 'logout']);
    
    // Appointment Management
    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index']);
        Route::get('/{id}', [AppointmentController::class, 'show']);
        Route::get('/{id}/edit', [AppointmentController::class, 'edit']);
        Route::put('/{id}', [AppointmentController::class, 'update']);
        Route::delete('/{id}', [AppointmentController::class, 'destroy']);
    });
    
    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index']);
        Route::get('/create', [UserManagementController::class, 'create']);
        Route::post('/', [UserManagementController::class, 'store']);
        Route::get('/{id}', [UserManagementController::class, 'show']);
        Route::get('/{id}/edit', [UserManagementController::class, 'edit']);
        Route::put('/{id}', [UserManagementController::class, 'update']);
        Route::delete('/{id}', [UserManagementController::class, 'destroy']);
    });
    
    // Hospital Management
    Route::prefix('hospitals')->group(function () {
        Route::get('/', [HospitalController::class, 'index']);
        Route::get('/create', [HospitalController::class, 'create']);
        Route::post('/', [HospitalController::class, 'store']);
        Route::get('/{id}', [HospitalController::class, 'show']);
        Route::get('/{id}/edit', [HospitalController::class, 'edit']);
        Route::put('/{id}', [HospitalController::class, 'update']);
        Route::delete('/{id}', [HospitalController::class, 'destroy']);
    });
    
    // Feedback Management
    Route::prefix('feedback')->group(function () {
        Route::get('/', [FeedbackController::class, 'index']);
        Route::get('/statistics', [FeedbackController::class, 'statistics']);
        Route::get('/{id}', [FeedbackController::class, 'show']);
        Route::delete('/{id}', [FeedbackController::class, 'destroy']);
    });
    
    // Subscription Management
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::get('/{id}', [SubscriptionController::class, 'show']);
        Route::put('/{id}', [SubscriptionController::class, 'update']);
        Route::delete('/{id}', [SubscriptionController::class, 'destroy']);
    });
    
    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::get('/edit', [ProfileController::class, 'edit']);
        Route::put('/', [ProfileController::class, 'update']);
    });
});

// API fallback for 404s
Route::fallback(function() {
    return response()->json([
        'success' => false,
        'message' => 'Backend API endpoint not found',
        'debug' => [
            'path' => request()->path(),
            'method' => request()->method(),
            'url' => request()->fullUrl()
        ]
    ], 404);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are for the frontend user-facing API functionality
| All routes return JSON responses
*/

// Test route to verify frontend API is working
Route::get('/test', function() {
    return response()->json([
        'success' => true,
        'message' => 'Frontend API is working!',
        'timestamp' => now(),
        'service' => 'CareConnect Frontend API'
    ]);
});

// Public Routes (No Authentication Required)
Route::get('/', [HomeController::class, 'index']);
Route::get('/about', [HomeController::class, 'about']);

// Hospital Routes (Public)
Route::prefix('hospitals')->group(function () {
    Route::get('/', [HospitalController::class, 'index']);
    Route::get('/{hospital}', [HospitalController::class, 'show']);
});

// Authentication Routes (Public)
Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/hospital-register', [AuthController::class, 'showHospitalRegister']);
    Route::post('/hospital-register', [AuthController::class, 'hospitalRegister']);
});

// Public Feedback Routes
Route::prefix('feedback')->group(function () {
    Route::get('/', [FeedbackController::class, 'index']);
    Route::post('/', [FeedbackController::class, 'store']);
});

// Protected Routes - Require Authentication
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'getUser']);
    
    // User Routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [AuthController::class, 'getProfile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        
        // User Appointments
        Route::get('/appointments', [AppointmentController::class, 'index']);
        Route::get('/appointments/create', [AppointmentController::class, 'create']);
        Route::post('/appointments', [AppointmentController::class, 'store']);
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
        Route::get('/appointments/{appointment}/receipt', [AppointmentController::class, 'receipt']);
        
        // User Feedback
        Route::get('/feedback', [FeedbackController::class, 'userIndex']);
        Route::post('/feedback', [FeedbackController::class, 'store']);
    });
    
    // Hospital Admin Routes
    Route::middleware(['role:hospital_admin'])->prefix('hospital')->group(function () {
        Route::get('/dashboard', [HospitalController::class, 'dashboard']);
        
        // Hospital Appointments Management
        Route::get('/appointments', [HospitalController::class, 'appointments']);
        Route::put('/appointments/{appointment}', [HospitalController::class, 'updateAppointment']);
        
        // Hospital Profile Management
        Route::get('/profile', [HospitalController::class, 'profile']);
        Route::put('/profile', [HospitalController::class, 'updateProfile']);
        
        // Hospital Subscription Management
        Route::get('/subscription', [HospitalController::class, 'subscription']);
        Route::put('/subscription', [HospitalController::class, 'updateSubscription']);
        Route::put('/subscription/cancel', [HospitalController::class, 'cancelSubscription']);
        
        // Hospital Feedback
        Route::get('/feedback', [HospitalController::class, 'hospitalFeedback']);
        Route::post('/feedback', [HospitalController::class, 'storeHospitalFeedback']);
    });
});

// Token validation endpoint
Route::middleware('auth:sanctum')->get('/validate-token', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Token is valid',
        'user' => $request->user()
    ]);
});

// API fallback for 404s
Route::fallback(function() {
    return response()->json([
        'success' => false,
        'message' => 'Frontend API endpoint not found',
        'debug' => [
            'path' => request()->path(),
            'method' => request()->method(),
            'url' => request()->fullUrl()
        ]
    ], 404);
});

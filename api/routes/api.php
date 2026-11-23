<?php

use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\HospitalPortalController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CareConnect API v1  —  every URL below starts with /api/v1
|--------------------------------------------------------------------------
| Public            no token
| Members           Bearer token from POST /auth/login     (patients + hospital admins)
| Admin panel       Bearer token from POST /admin/auth/login
*/

Route::prefix('v1')->group(function () {

    // ---- Public -------------------------------------------------------------
    Route::get('home', [PublicController::class, 'home']);
    Route::get('hospitals', [PublicController::class, 'hospitals']);
    Route::get('hospitals/{hospital}', [PublicController::class, 'hospital']);

    Route::middleware('throttle:auth')->group(function () {
        Route::post('auth/login', [AuthController::class, 'login']);
        Route::post('auth/register', [AuthController::class, 'register']);
        Route::post('auth/register-hospital', [AuthController::class, 'registerHospital']);
        Route::post('admin/auth/login', [Admin\AuthController::class, 'login']);
    });

    // ---- Patients + hospital admins ----------------------------------------
    Route::middleware(['auth:sanctum', 'account:member'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('me', [ProfileController::class, 'update']);           // POST: may contain a file
        Route::put('me/password', [ProfileController::class, 'updatePassword']);
        Route::post('feedback', [FeedbackController::class, 'store']);

        Route::middleware('account:user')->group(function () {
            Route::get('bookable-hospitals', [AppointmentController::class, 'bookableHospitals']);
            Route::get('appointments', [AppointmentController::class, 'index']);
            Route::post('appointments', [AppointmentController::class, 'store']);
            Route::get('appointments/{appointment}', [AppointmentController::class, 'show']);
        });

        Route::middleware('account:hospital_admin')->prefix('my-hospital')->group(function () {
            Route::get('/', [HospitalPortalController::class, 'show']);
            Route::post('/', [HospitalPortalController::class, 'update']);    // POST: may contain a file
            Route::get('dashboard', [HospitalPortalController::class, 'dashboard']);
            Route::get('appointments', [HospitalPortalController::class, 'appointments']);
            Route::patch('appointments/{appointment}', [HospitalPortalController::class, 'updateAppointment']);
            Route::put('subscription', [HospitalPortalController::class, 'updateSubscription']);
            Route::post('subscription/cancel', [HospitalPortalController::class, 'cancelSubscription']);
        });
    });

    // ---- Admin panel -------------------------------------------------------
    Route::middleware(['auth:sanctum', 'account:admin'])->prefix('admin')->group(function () {
        Route::post('auth/logout', [Admin\AuthController::class, 'logout']);
        Route::get('me', [Admin\AuthController::class, 'me']);
        Route::post('me', [Admin\AuthController::class, 'update']);
        Route::put('me/password', [Admin\AuthController::class, 'updatePassword']);

        Route::get('dashboard', Admin\DashboardController::class);

        // POST to /{id} updates (multipart forms can't use PUT in PHP).
        Route::apiResource('users', Admin\UserController::class)->except('update');
        Route::post('users/{user}', [Admin\UserController::class, 'update']);
        Route::apiResource('hospitals', Admin\HospitalController::class)->except('update');
        Route::post('hospitals/{hospital}', [Admin\HospitalController::class, 'update']);
        Route::apiResource('appointments', Admin\AppointmentController::class)->except('store');
        Route::apiResource('subscriptions', Admin\SubscriptionController::class)->except('store');
        Route::apiResource('feedback', Admin\FeedbackController::class)->only(['index', 'destroy'])
            ->parameters(['feedback' => 'feedback']);
    });
});

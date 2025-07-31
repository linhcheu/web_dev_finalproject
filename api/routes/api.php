<?php

// Import the Route class to define routes
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Import backend controllers with aliases to avoid name conflicts
use App\Http\Controllers\backendController\DashboardController;
use App\Http\Controllers\backendController\AppointmentController as BackendAppointmentController; 
use App\Http\Controllers\backendController\UserManagementController;
use App\Http\Controllers\backendController\HospitalController as BackendHospitalController;
use App\Http\Controllers\backendController\FeedbackController as BackendFeedbackController;
use App\Http\Controllers\backendController\SubscriptionController;
use App\Http\Controllers\backendController\AdminAuthController;
use App\Http\Controllers\backendController\ProfileController;

// Import frontend controllers
use App\Http\Controllers\frontendController\HomeController;
use App\Http\Controllers\frontendController\AuthController;
use App\Http\Controllers\frontendController\HospitalController as FrontendHospitalController;
use App\Http\Controllers\frontendController\AppointmentController as FrontendAppointmentController;
use App\Http\Controllers\frontendController\FeedbackController as FrontendFeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes return JSON responses for API functionality
*/

// Test route to verify API is working
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!', 'timestamp' => now()]);
});

// Authentication test route - useful for debugging token issues
Route::middleware('auth:sanctum')->get('/auth-test', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Authentication is working correctly!',
        'user' => $request->user(),
        'timestamp' => now()
    ]);
});

// BACKEND ROUTES - Admin panel API
Route::prefix('backend')->group(function () {
    // Public routes (admin login)
Route::prefix('auth')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm']);
    Route::post('/login', [AdminAuthController::class, 'login'])->name('api.login');
});
    
    // Protected routes - Require admin authentication
    Route::middleware('auth:sanctum')->group(function () {
        // Admin profile
        
        // Hospital management - add explicit edit route
        Route::apiResource('hospital', BackendHospitalController::class);
        Route::get('/hospital/{id}/edit', [BackendHospitalController::class, 'edit']);
        Route::put('/hospital/{id}', [BackendHospitalController::class, 'update']);
        Route::delete('/hospital/{id}', [BackendHospitalController::class, 'destroy']);
        // Dashboard
        Route::get('/', [DashboardController::class, 'index']);
        
        // Auth - logout (requires authentication)
        Route::post('/auth/logout', [AdminAuthController::class, 'logout']);
        
        // Appointment management
        Route::prefix('appointment')->group(function () {
            Route::get('/', [BackendAppointmentController::class, 'index']);
            Route::get('/{id}/edit', [BackendAppointmentController::class, 'edit']);
            Route::put('/{id}', [BackendAppointmentController::class, 'update']);
            Route::delete('/{id}', [BackendAppointmentController::class, 'destroy']);
        });

        // User management
        Route::prefix('user')->group(function () {
            Route::get('/', [UserManagementController::class, 'index']);
            Route::get('/{id}/edit', [UserManagementController::class, 'edit']);
            Route::put('/{id}', [UserManagementController::class, 'update']);
            Route::delete('/{id}', [UserManagementController::class, 'destroy']);
        });

        // Hospital management
        Route::prefix('hospital')->group(function () {
            Route::get('/', [BackendHospitalController::class, 'index']);
            Route::get('/{id}/edit', [BackendHospitalController::class, 'edit']);
            Route::put('/{id}', [BackendHospitalController::class, 'update']);
            Route::delete('/{id}', [BackendHospitalController::class, 'destroy']);
        });

        // Feedback management
        Route::prefix('feedback')->group(function () {
            Route::get('/', [BackendFeedbackController::class, 'index']);
            Route::get('/statistics', [BackendFeedbackController::class, 'statistics']);
            Route::get('/{id}', [BackendFeedbackController::class, 'show']);
            Route::delete('/{id}', [BackendFeedbackController::class, 'destroy']);
        });
        
        // Subscription management
        Route::prefix('subscription')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index']);
        });
        
        // Profile management
        Route::prefix('profile')->group(function () {
            Route::get('/edit', [ProfileController::class, 'edit']);
            Route::put('/', [ProfileController::class, 'update']);
        });
    });
});

// FRONTEND ROUTES
Route::prefix('frontend')->group(function () {
    // Public routes (no authentication required)
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/about', [HomeController::class, 'about']);
    Route::get('/hospitals', [FrontendHospitalController::class, 'index']);
    Route::get('/hospitals/{hospital}', [FrontendHospitalController::class, 'show']);
    Route::get('/subscription-plans', [HomeController::class, 'subscriptionPlans']);
    
    // Authentication - public routes
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/hospital-register', [AuthController::class, 'showHospitalRegister']);
    Route::post('/hospital-register', [AuthController::class, 'hospitalRegister']);
    
    // Debug route for checking feedback submission
    Route::post('/feedback-debug', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Feedback debug endpoint reached',
            'data' => [
                'request_data' => $request->all(),
                'headers' => $request->header()
            ]
        ]);
    });
    
    // Feedback routes - explicitly defined as public routes
    Route::get('/feedback', [FrontendFeedbackController::class, 'index']);
    Route::post('/feedback', [FrontendFeedbackController::class, 'store']);
    Route::get('/hospital/{hospital}/feedback', [FrontendFeedbackController::class, 'hospitalIndex']);
    Route::post('/hospital/{hospital}/feedback', [FrontendFeedbackController::class, 'hospitalStore']);
    
    // Token validation endpoint - useful for testing
    Route::middleware('auth:sanctum')->get('/validate-token', [AuthController::class, 'validateToken']);
    
    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // User features
        Route::prefix('user')->group(function () {
            Route::get('/profile', [AuthController::class, 'getUserProfile']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            
            // User-specific feedback routes
            Route::get('/feedback', [FrontendFeedbackController::class, 'getUserFeedback']);
            Route::post('/feedback', [FrontendFeedbackController::class, 'store']);
            
            // Appointments
            Route::get('/appointments', [FrontendAppointmentController::class, 'index']);
            Route::get('/appointments/create', [FrontendAppointmentController::class, 'create']);
            Route::post('/appointments', [FrontendAppointmentController::class, 'store']);
            Route::get('/appointments/{appointment}', [FrontendAppointmentController::class, 'show']);
            Route::get('/appointments/{appointment}/receipt', [FrontendAppointmentController::class, 'receipt']);
        });
        
        // Hospital admin features
        Route::prefix('hospital')->group(function () {
            Route::get('/dashboard', [FrontendHospitalController::class, 'dashboard']);
            Route::get('/appointments', [FrontendHospitalController::class, 'appointments']);
            Route::put('/appointments/{appointmentId}', [FrontendHospitalController::class, 'updateAppointment']);
            Route::get('/profile', [FrontendHospitalController::class, 'profile']);
            Route::put('/profile', [FrontendHospitalController::class, 'updateProfile']);
            
            // Add debug endpoints for subscription
            Route::get('/auth-check', function (Request $request) {
                return response()->json([
                    'success' => true,
                    'user' => $request->user(),
                    'token' => $request->bearerToken() ? 'Valid token' : 'No token'
                ]);
            });
            
            // Subscription routes
            Route::get('/subscription', [FrontendHospitalController::class, 'subscription']);
            Route::put('/subscription', [FrontendHospitalController::class, 'updateSubscription']);
            Route::put('/subscription/cancel', [FrontendHospitalController::class, 'cancelSubscription']);
            
            // Hospital feedback routes
            Route::get('/feedback', [FrontendHospitalController::class, 'hospitalFeedback']); // Returns form structure
            Route::get('/feedback/data', [FrontendHospitalController::class, 'getHospitalFeedback']); // Returns feedback data
            Route::post('/feedback', [FrontendHospitalController::class, 'submitHospitalFeedback']); // Submits feedback
            
            // Add a debug route to check method support
            Route::match(['GET', 'POST', 'PUT', 'DELETE'], '/test-methods', function() {
                return response()->json([
                    'success' => true,
                    'message' => 'Your HTTP method was accepted',
                    'method' => request()->method()
                ]);
            });
        });
    });
});

// Add a debug route to verify token and hospital admin status
Route::middleware('auth:sanctum')->get('/frontend/debug/hospital-admin', function (Request $request) {
    $user = $request->user();
    $hospital = null;
    
    if ($user && $user->role === 'hospital_admin') {
        $hospital = \App\Models\frontendModels\Hospital::where('owner_id', $user->user_id)->first();
    }
    
    return response()->json([
        'success' => true,
        'token_valid' => true,
        'user' => [
            'id' => $user->user_id,
            'email' => $user->email,
            'role' => $user->role,
            'is_hospital_admin' => $user->role === 'hospital_admin'
        ],
        'hospital' => $hospital ? [
            'id' => $hospital->hospital_id,
            'name' => $hospital->name,
            'status' => $hospital->status
        ] : null,
        'auth_success' => $user->role === 'hospital_admin' && $hospital !== null
    ]);
});

// API fallback for 404s
Route::fallback(function() {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'debug' => [
            'path' => request()->path(),
            'method' => request()->method(),
            'url' => request()->fullUrl()
        ]
    ], 404);
});








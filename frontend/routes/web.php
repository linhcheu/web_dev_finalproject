<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/hospitals', [HospitalController::class, 'index'])->name('hospitals.index');
// Route 1: Shows details for a specific hospital using a controller method.
// This route uses a controller because it likely needs to fetch hospital data from the database.
Route::get('/hospitals/{hospital}', [HospitalController::class, 'show'])->name('hospitals.show');

// Route 2: Displays the subscription plans page using a closure that returns a view directly.
// This route does NOT use a controller because it only returns a static view and does not require any dynamic data or logic.
Route::get('/subscription-plans', function () {
    return view('subscription_view');
})->name('subscription_view');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/hospital-register', [AuthController::class, 'showHospitalRegister'])->name('hospital.register');
    Route::post('/hospital-register', [AuthController::class, 'hospitalRegister']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// User protected routes
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('appointments');
    })->name('dashboard');
    
    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/receipt', [AppointmentController::class, 'receipt'])->name('appointments.receipt');
    // (Removed duplicate route. The correct route is already defined above.)
    
    // User profile
    Route::get('/profile', function () {
        return view('user.profile');
    })->name('profile');
    Route::put('/profile', function (Request $request) {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users_table,email,' . auth()->id() . ',user_id',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        $updateData = $request->only(['first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'address']);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                \Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        $user->update($updateData);

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    })->name('profile.update');
    
    // Feedback
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('user.feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

// Hospital admin protected routes
// This middleware group ensures that only authenticated users with the 'hospital_admin' role can access the enclosed routes.
// 'auth' checks if the user is logged in.
// 'role:hospital_admin' checks if the logged-in user has the 'hospital_admin' role.
// Note: The 'auth' and 'role:hospital_admin' middleware are provided by Laravel and/or packages like spatie/laravel-permission.
// If you do not see custom middleware in your backend folder, they are likely registered in app/Http/Kernel.php or provided by installed packages.
Route::middleware(['auth', 'role:hospital_admin'])->group(function () {
    Route::get('/hospital/dashboard', [HospitalController::class, 'dashboard'])->name('hospital.dashboard');
    Route::get('/hospital/appointments', [HospitalController::class, 'appointments'])->name('hospital.appointments');
    Route::put('/hospital/appointments/{appointment}', [HospitalController::class, 'updateAppointment'])->name('hospital.appointments.update');
    Route::get('/hospital/profile', [HospitalController::class, 'profile'])->name('hospital.profile');
    Route::put('/hospital/profile', [HospitalController::class, 'updateProfile'])->name('hospital.profile.update');
    Route::get('/hospital/subscription', [HospitalController::class, 'subscription'])->name('hospital.subscription');
    Route::put('/hospital/subscription', [HospitalController::class, 'updateSubscription'])->name('hospital.subscription.update');
    Route::put('/hospital/subscription/cancel', [HospitalController::class, 'cancelSubscription'])->name('hospital.subscription.cancel');
    
    // Hospital Feedback
    Route::get('/hospital/feedback', [FeedbackController::class, 'hospitalIndex'])->name('hospital.feedback');
    Route::post('/hospital/feedback', [FeedbackController::class, 'hospitalStore'])->name('hospital.feedback.store');
});


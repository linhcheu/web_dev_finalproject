<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\Feedback;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get counts for dashboard stats
        $totalUsers = User::count();
        $totalAppointments = Appointment::count();
        $totalHospitals = Hospital::count();
        $totalFeedback = Feedback::count();

        // Get recent activities
        $recentUsers = User::latest()->take(5)->get();
        $recentAppointments = Appointment::with(['user', 'hospital'])->latest()->take(5)->get();
        $recentHospitals = Hospital::latest()->take(5)->get();

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => [
                        'total_users' => $totalUsers,
                        'total_appointments' => $totalAppointments,
                        'total_hospitals' => $totalHospitals,
                        'total_feedback' => $totalFeedback
                    ],
                    'recent_activities' => [
                        'recent_users' => $recentUsers->map(function($user) {
                            return [
                                'id' => $user->user_id,
                                'name' => $user->first_name . ' ' . $user->last_name,
                                'email' => $user->email,
                                'role' => $user->role,
                                'created_at' => $user->created_at
                            ];
                        }),
                        'recent_appointments' => $recentAppointments->map(function($appointment) {
                            return [
                                'id' => $appointment->appointment_id,
                                'patient_name' => $appointment->user ? $appointment->user->first_name . ' ' . $appointment->user->last_name : 'N/A',
                                'hospital_name' => $appointment->hospital ? $appointment->hospital->name : 'N/A',
                                'appointment_date' => $appointment->appointment_date,
                                'appointment_time' => $appointment->appointment_time,
                                'status' => $appointment->status,
                                'created_at' => $appointment->created_at
                            ];
                        }),
                        'recent_hospitals' => $recentHospitals->map(function($hospital) {
                            return [
                                'id' => $hospital->hospital_id,
                                'name' => $hospital->name,
                                'location' => $hospital->location,
                                'status' => $hospital->status,
                                'created_at' => $hospital->created_at
                            ];
                        })
                    ]
                ]
            ]);
        }

        return view('dashboard', compact(
            'totalUsers',
            'totalAppointments', 
            'totalHospitals',
            'totalFeedback',
            'recentUsers',
            'recentAppointments',
            'recentHospitals'
        ));
    }
} 
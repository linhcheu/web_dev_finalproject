<?php

namespace App\Http\Controllers\backendController;

use App\Models\backendModels\User;
use App\Models\backendModels\Appointment;
use App\Models\backendModels\Hospital;
use App\Models\backendModels\Feedback;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
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

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalAppointments' => $totalAppointments,
            'totalHospitals' => $totalHospitals,
            'totalFeedback' => $totalFeedback,
            'recentUsers' => $recentUsers,
            'recentAppointments' => $recentAppointments,
            'recentHospitals' => $recentHospitals
        ]);
    }
} 
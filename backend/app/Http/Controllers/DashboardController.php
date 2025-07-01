<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\Feedback;
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
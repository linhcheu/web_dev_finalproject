<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $data = $this->api->get('admin/dashboard')['data'];

        return view('dashboard', [
            'totalUsers' => $data['stats']['total_users'],
            'totalAppointments' => $data['stats']['total_appointments'],
            'totalHospitals' => $data['stats']['total_hospitals'],
            'totalFeedback' => $data['stats']['total_feedback'],
            'recentUsers' => User::collectionFromApi($data['recent_users']),
            'recentAppointments' => Appointment::collectionFromApi($data['recent_appointments']),
            'recentHospitals' => Hospital::collectionFromApi($data['recent_hospitals']),
        ]);
    }
}

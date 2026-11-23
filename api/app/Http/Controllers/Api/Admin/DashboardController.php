<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Feedback;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return $this->ok([
            'stats' => [
                'total_users' => User::count(),
                'total_appointments' => Appointment::count(),
                'total_hospitals' => Hospital::count(),
                'total_feedback' => Feedback::count(),
            ],
            'recent_users' => User::latest()->take(5)->get(),
            'recent_appointments' => Appointment::with(['user', 'hospital'])->latest()->take(5)->get(),
            'recent_hospitals' => Hospital::latest()->take(5)->get(),
        ]);
    }
}

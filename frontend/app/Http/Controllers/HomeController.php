<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Hospital;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $data = $this->api->get('home')['data'];

        return view('home', [
            'popularHospitals' => Hospital::collectionFromApi($data['popular_hospitals']),
            'recentFeedback' => Feedback::collectionFromApi($data['recent_feedback']),
        ]);
    }
}

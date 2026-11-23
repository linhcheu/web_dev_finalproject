<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        return view('user.feedback');
    }

    public function hospitalIndex(): View
    {
        return view('hospital.feedback');
    }

    /** Patients and hospital admins both post feedback here. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['comments' => 'required|string|min:5|max:1000']);

        $this->api->post('feedback', $data);

        return back()->with('success', 'Thank you for your feedback! We appreciate your input.');
    }
}

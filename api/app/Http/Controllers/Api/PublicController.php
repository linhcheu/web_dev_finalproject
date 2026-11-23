<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Data anyone can see, without logging in.
 */
class PublicController extends Controller
{
    public function home(): JsonResponse
    {
        return $this->ok([
            'popular_hospitals' => Hospital::active()->with('subscription')->latest()->take(6)->get(),
            'recent_feedback' => Feedback::with('user:user_id,first_name,last_name,role,profile_picture')
                ->whereHas('user')
                ->latest()->take(10)->get(),
        ]);
    }

    public function hospitals(Request $request): JsonResponse
    {
        $request->validate(['province' => 'nullable|string|max:100']);

        $hospitals = Hospital::bookable()
            ->with('subscription')
            ->when($request->filled('province'), fn ($q) => $q->where('province', $request->province))
            ->latest()
            ->get();

        return $this->ok($hospitals);
    }

    public function hospital(Hospital $hospital): JsonResponse
    {
        abort_unless($hospital->status === 'active', 404);

        return $this->ok($hospital->load('subscription'));
    }
}

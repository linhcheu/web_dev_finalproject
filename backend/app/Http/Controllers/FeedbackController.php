<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $feedback = $this->api->paginate('admin/feedback', Feedback::class, $request->only('page'));

        return view('feedback', ['feedback' => $feedback, 'totalFeedback' => $feedback->extra['total_all']]);
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['success' => true] + $this->api->delete("admin/feedback/$id"));
    }
}

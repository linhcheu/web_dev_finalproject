<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->paginated(Feedback::with('user')->latest()->paginate(10), ['total_all' => Feedback::count()]);
    }

    public function destroy(Feedback $feedback): JsonResponse
    {
        $feedback->delete();

        return $this->ok(null, 'Feedback deleted successfully!');
    }
}

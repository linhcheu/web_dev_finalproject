<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['comments' => 'required|string|min:5|max:1000']);

        $feedback = $request->user()->feedback()->create($data);

        return $this->ok($feedback, 'Thank you for your feedback!', 201);
    }
}

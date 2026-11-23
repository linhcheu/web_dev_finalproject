<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * A patient's own appointments.
 */
class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate(['date' => 'nullable|date']);

        $appointments = $request->user()->appointments()
            ->with('hospital')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('appointment_date', $request->date))
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(10);

        return $this->paginated($appointments);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'hospital_id' => ['required', 'integer', Rule::in(Hospital::bookable()->pluck('hospital_id'))],
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'symptom' => 'required|string|max:500',
            'patient_phone' => 'required|string|min:8|max:20',
        ], ['hospital_id.in' => 'This hospital is not accepting appointments.']);

        $appointment = $request->user()->appointments()->create($data);

        return $this->ok($appointment->load(['hospital', 'user']), 'Appointment booked successfully!', 201);
    }

    public function show(Request $request, Appointment $appointment): JsonResponse
    {
        abort_unless($appointment->user_id === $request->user()->user_id, 404);

        return $this->ok($appointment->load(['hospital', 'user']));
    }

    public function bookableHospitals(): JsonResponse
    {
        return $this->ok(Hospital::bookable()->orderBy('name')->get(['hospital_id', 'name', 'province', 'location']));
    }
}

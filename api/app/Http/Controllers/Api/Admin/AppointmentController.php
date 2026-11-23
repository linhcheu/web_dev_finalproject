<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        [$sortBy, $sortOrder] = $this->sorting(['appointment_id', 'appointment_date', 'appointment_time', 'status', 'created_at'], 'appointment_id');

        $appointments = Appointment::with(['user', 'hospital'])
            ->when($request->filled('appointment_id'), fn ($q) => $q->where('appointment_id', (int) $request->appointment_id))
            ->when(in_array($request->status, Appointment::STATUSES, true), fn ($q) => $q->where('status', $request->status))
            ->when(is_numeric($request->hospital_id), fn ($q) => $q->where('hospital_id', (int) $request->hospital_id))
            ->when(is_numeric($request->user_id), fn ($q) => $q->where('user_id', (int) $request->user_id))
            ->when($request->filled('appointment_date'), fn ($q) => $q->whereDate('appointment_date', $request->date('appointment_date')))
            ->orderBy($sortBy, $sortOrder)
            ->paginate(10);

        return $this->paginated($appointments, [
            'total_all' => Appointment::count(),
            'filters' => $this->options(),
        ]);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        return $this->ok($appointment->load(['user', 'hospital']), null, 200, ['options' => $this->options()]);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $appointment->update($request->validate([
            'user_id' => 'sometimes|required|integer|exists:users_table,user_id',
            'hospital_id' => 'sometimes|required|integer|exists:hospitals,hospital_id',
            'appointment_date' => 'sometimes|required|date',
            'appointment_time' => 'sometimes|required|date_format:H:i',
            'patient_phone' => 'sometimes|required|string|max:20',
            'symptom' => 'sometimes|nullable|string|max:500',
            'status' => ['sometimes', 'required', Rule::in(Appointment::STATUSES)],
        ]));

        return $this->ok($appointment->fresh(['user', 'hospital']), 'Appointment updated successfully!');
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $appointment->delete();

        return $this->ok(null, 'Appointment deleted successfully!');
    }

    /** Dropdown options for filters and the edit form. */
    private function options(): array
    {
        return [
            'hospitals' => Hospital::orderBy('name')->get(['hospital_id', 'name', 'province']),
            'users' => User::where('role', User::ROLE_USER)->orderBy('first_name')->get(['user_id', 'first_name', 'last_name', 'email']),
            'statuses' => Appointment::STATUSES,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $appointments = $this->api->paginate('admin/appointments', Appointment::class, $request->only(
            'appointment_id', 'status', 'hospital_id', 'user_id', 'appointment_date', 'sort_by', 'sort_order', 'page'
        ));
        $filters = $appointments->extra['filters'];

        return view('appointment', [
            'appointments' => $appointments,
            'totalAppointments' => $appointments->extra['total_all'],
            'hospitals' => Hospital::collectionFromApi($filters['hospitals']),
            'users' => User::collectionFromApi($filters['users']),
            'statuses' => $filters['statuses'],
            'sortBy' => $request->input('sort_by', 'appointment_id'),
            'sortOrder' => $request->input('sort_order', 'asc'),
        ]);
    }

    public function edit(int $id): View
    {
        $response = $this->api->get("admin/appointments/$id");

        return view('appointment_edit', [
            'appointment' => Appointment::fromApi($response['data']),
            'users' => User::collectionFromApi($response['options']['users']),
            'hospitals' => Hospital::collectionFromApi($response['options']['hospitals']),
        ]);
    }

    /** "basic_info" (patient, hospital, date, time) or "status" form. */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->input('update_type') === 'status'
            ? $request->validate(['status' => 'required|in:upcoming,completed,no_show'])
            : $request->validate([
                'user_id' => 'required|integer',
                'hospital_id' => 'required|integer',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required|date_format:H:i,H:i:s',
                'patient_phone' => 'sometimes|required|string|max:20',
                'symptom' => 'sometimes|nullable|string|max:500',
            ]);

        if (isset($data['appointment_time'])) {
            $data['appointment_time'] = substr($data['appointment_time'], 0, 5); // "09:30:00" -> "09:30"
        }

        $this->api->put("admin/appointments/$id", $data);

        return redirect()->route('appointment.edit', $id)->with('success', 'Appointment updated successfully!');
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['success' => true] + $this->api->delete("admin/appointments/$id"));
    }
}

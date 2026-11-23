<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Hospital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** A patient's appointments. */
class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['date' => 'nullable|date']);

        return view('appointments.index', [
            'appointments' => $this->api->paginate('appointments', Appointment::class, $request->only('date', 'page')),
        ]);
    }

    public function create(): View
    {
        return view('appointments.create', [
            'hospitals' => Hospital::collectionFromApi($this->api->get('bookable-hospitals')['data']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hospital_id' => 'required|integer',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'symptom' => 'required|string|max:500',
            'patient_phone' => 'required|string|min:8|max:20',
        ]);

        $appointment = $this->api->post('appointments', $data)['data'];

        return redirect()->route('appointments.receipt', $appointment['appointment_id'])
            ->with('success', 'Appointment booked successfully!');
    }

    public function show(int $appointment): View
    {
        return view('appointments.show', ['appointment' => $this->find($appointment)]);
    }

    public function receipt(int $appointment): View
    {
        return view('appointments.receipt', ['appointment' => $this->find($appointment)]);
    }

    private function find(int $id): Appointment
    {
        return Appointment::fromApi($this->api->get("appointments/$id")['data']);
    }
}

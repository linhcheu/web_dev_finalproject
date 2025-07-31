<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Hospital;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->appointments()
            ->with('hospital')
            ->orderBy('appointment_date', 'asc');

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->paginate(10);
        return view('appointments.index', compact('appointments'));
    }
    
    public function create()
    {
        $hospitals = Hospital::active()
            ->withActiveSubscription()
            ->get();
        return view('appointments.create', compact('hospitals'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'hospital_id' => 'required|exists:hospitals,hospital_id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'symptom' => 'required|string|max:500',
            'patient_phone' => 'required|string|min:8|max:20',
        ]);
        
        $appointment = auth()->user()->appointments()->create([
            'hospital_id' => $request->hospital_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'symptom' => $request->symptom,
            'patient_phone' => $request->patient_phone,
        ]);
        
        return redirect()->route('appointments.receipt', $appointment)
            ->with('success', 'Appointment booked successfully!');
    }
    
    public function show(Appointment $appointment)
    {
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }
        
        $appointment->load('hospital');
        return view('appointments.show', compact('appointment'));
    }
    
    public function receipt(Appointment $appointment)
    {
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }
        
        $appointment->load('hospital');
        return view('appointments.receipt', compact('appointment'));
    }
}

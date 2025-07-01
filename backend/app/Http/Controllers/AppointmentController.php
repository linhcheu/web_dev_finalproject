<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Hospital;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $totalAppointments = Appointment::count();
        $appointments = Appointment::with(['user', 'hospital'])
            ->latest()
            ->paginate(10);

        return view('appointment', compact('totalAppointments', 'appointments'));
    }

    public function edit($id)
    {
        $appointment = Appointment::with(['user', 'hospital'])->findOrFail($id);
        $users = User::all();
        $hospitals = Hospital::all();
        
        return view('appointment_edit', compact('appointment', 'users', 'hospitals'));
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $updateType = $request->input('update_type');

        switch ($updateType) {
            case 'basic_info':
                return $this->updateBasicInfo($request, $appointment);
            
            case 'status':
                return $this->updateStatus($request, $appointment);
            
            default:
                return redirect()->route('appointment.edit', $appointment->appointment_id)->with('error', 'Invalid update type.');
        }
    }

    private function updateBasicInfo(Request $request, $appointment)
    {
        $request->validate([
            'user_id' => 'required|exists:users_table,user_id',
            'hospital_id' => 'required|exists:hospitals,hospital_id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'notes' => 'nullable|string|max:500',
        ]);

        $appointment->update([
            'user_id' => $request->user_id,
            'hospital_id' => $request->hospital_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointment.edit', $appointment->appointment_id)->with('success', 'Appointment information updated successfully!');
    }

    private function updateStatus(Request $request, $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,rescheduled',
        ]);

        $appointment->update(['status' => $request->status]);

        return redirect()->route('appointment.edit', $appointment->appointment_id)->with('success', 'Appointment status updated successfully!');
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully!'
        ]);
    }
} 
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['user', 'hospital']);
        
        if ($request->user()->role !== 'hospital_admin') {
            $query->where('user_id', $request->user()->user_id);
        }
        
        $appointments = $query->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hospital_id' => 'required|exists:hospitals,hospital_id',
            'appointment_date' => 'required|date|after:today',
            'symptom' => 'required|string',
        ]);

        $appointment = Appointment::create([
            'user_id' => $request->user()->user_id,
            'hospital_id' => $request->hospital_id,
            'appointment_date' => $request->appointment_date,
            'symptom' => $request->symptom,
            'status' => 'upcoming'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully',
            'data' => $appointment->load(['user', 'hospital'])
        ], 201);
    }

    public function show(Appointment $appointment)
    {
        if ($appointment->user_id !== request()->user()->user_id && request()->user()->role !== 'hospital_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $appointment->load(['user', 'hospital'])
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        if ($appointment->user_id !== $request->user()->user_id && $request->user()->role !== 'hospital_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'hospital_id' => 'sometimes|required|exists:hospitals,hospital_id',
            'appointment_date' => 'sometimes|required|date',
            'symptom' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:upcoming,completed,cancelled,no_show',
        ]);

        $appointment->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully',
            'data' => $appointment->load(['user', 'hospital'])
        ]);
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->user_id !== request()->user()->user_id && request()->user()->role !== 'hospital_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully'
        ]);
    }
} 
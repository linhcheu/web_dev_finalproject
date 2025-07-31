<?php

namespace App\Http\Controllers\backendController;

use App\Models\backendModels\Appointment;
use App\Models\backendModels\User;
use App\Models\backendModels\Hospital;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['user', 'hospital']);
        
        // Filter by ID
        if ($request->filled('appointment_id')) {
            $query->where('appointment_id', 'LIKE', '%' . $request->appointment_id . '%');
        }
        
        // Filter by status - only upcoming and completed
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by hospital
        if ($request->filled('hospital_id') && $request->hospital_id !== 'all') {
            $query->where('hospital_id', $request->hospital_id);
        }
        
        // Filter by user - only users with 'user' role
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by appointment date
        if ($request->filled('appointment_date')) {
            $query->whereDate('appointment_date', $request->appointment_date);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'appointment_id');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Validate sort fields
        $allowedSortFields = ['appointment_id', 'appointment_date', 'appointment_time', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'appointment_id';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }
        
        $query->orderBy($sortBy, $sortOrder);
        
        $totalAppointments = Appointment::count();
        $appointments = $query->paginate(10)->withQueryString();
        
        // Get data for filter dropdowns
        $hospitals = Hospital::orderBy('name')->get();
        $users = User::where('role', 'user')->orderBy('first_name')->get();
        $statuses = ['upcoming', 'completed'];
        
        return response()->json([
            'totalAppointments' => $totalAppointments,
            'appointments' => $appointments,
            'hospitals' => $hospitals,
            'users' => $users,
            'statuses' => $statuses,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ]);
    }

    public function edit($id)
    {
        $appointment = Appointment::with(['user', 'hospital'])->findOrFail($id);
        $users = User::all();
        $hospitals = Hospital::all();
        
        return response()->json([
            'appointment' => $appointment,
            'users' => $users,
            'hospitals' => $hospitals
        ]);
    }

    /**
     * Update the specified appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Log incoming request data for debugging
        \Log::info('Appointment update request', [
            'id' => $id,
            'data' => $request->all(),
            'content_type' => $request->header('Content-Type')
        ]);

        // Find the appointment
        $appointment = Appointment::find($id);
        
        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ], 404);
        }
        
        // Validate the request data
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'appointment_date' => 'sometimes|date',
            'appointment_time' => 'sometimes|string',
            'status' => 'sometimes|string|in:pending,confirmed,completed,cancelled,rescheduled',
            'reason' => 'sometimes|string',
            'notes' => 'sometimes|nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update the appointment with validated data
        $appointment->update($validator->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully',
            'data' => [
                'appointment' => $appointment->fresh()
            ]
        ]);
    }

    private function updateBasicInfo(Request $request, $appointment)
    {
        $request->validate([
            'user_id' => 'required|exists:users_table,user_id',
            'hospital_id' => 'required|exists:hospitals,hospital_id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'patient_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        $appointment->update([
            'user_id' => $request->user_id,
            'hospital_id' => $request->hospital_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'patient_phone' => $request->patient_phone,
            'notes' => $request->notes,
        ]);

        // Reload relationships for full response
        $appointment->load(['user', 'hospital']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment information updated successfully!',
            'data' => $appointment
        ]);
    }

    private function updateStatus(Request $request, $appointment)
    {
        $request->validate([
            'status' => 'required|in:upcoming,completed',
        ]);

        $appointment->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully!',
            'data' => $appointment->fresh()
        ]);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $data = $appointment->toArray(); // Save data before deletion
        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully!',
            'data' => $data
        ]);
    }
      
}
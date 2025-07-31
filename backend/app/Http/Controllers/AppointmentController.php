<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Hospital;
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

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'appointments' => $appointments->items(),
                    'pagination' => [
                        'current_page' => $appointments->currentPage(),
                        'last_page' => $appointments->lastPage(),
                        'per_page' => $appointments->perPage(),
                        'total' => $appointments->total()
                    ],
                    'filters' => [
                        'hospitals' => $hospitals,
                        'users' => $users,
                        'statuses' => $statuses
                    ],
                    'sort' => [
                        'sort_by' => $sortBy,
                        'sort_order' => $sortOrder
                    ],
                    'total_appointments' => $totalAppointments
                ]
            ]);
        }
        
        return view('appointment', compact('totalAppointments', 'appointments', 'hospitals', 'users', 'statuses', 'sortBy', 'sortOrder'));
    }

    public function show(Request $request, $id)
    {
        $appointment = Appointment::with(['user', 'hospital'])->findOrFail($id);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'appointment' => [
                        'id' => $appointment->appointment_id,
                        'user_id' => $appointment->user_id,
                        'hospital_id' => $appointment->hospital_id,
                        'appointment_date' => $appointment->appointment_date,
                        'appointment_time' => $appointment->appointment_time,
                        'patient_phone' => $appointment->patient_phone,
                        'symptom' => $appointment->symptom,
                        'status' => $appointment->status,
                        'created_at' => $appointment->created_at,
                        'updated_at' => $appointment->updated_at,
                        'user' => $appointment->user ? [
                            'id' => $appointment->user->user_id,
                            'name' => $appointment->user->first_name . ' ' . $appointment->user->last_name,
                            'email' => $appointment->user->email
                        ] : null,
                        'hospital' => $appointment->hospital ? [
                            'id' => $appointment->hospital->hospital_id,
                            'name' => $appointment->hospital->name,
                            'location' => $appointment->hospital->location
                        ] : null
                    ]
                ]
            ]);
        }

        return view('appointment_show', compact('appointment'));
    }

    public function edit(Request $request, $id)
    {
        $appointment = Appointment::with(['user', 'hospital'])->findOrFail($id);
        $users = User::all();
        $hospitals = Hospital::all();

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'appointment' => $appointment,
                    'users' => $users,
                    'hospitals' => $hospitals,
                    'form_fields' => [
                        'user_id' => [
                            'type' => 'select',
                            'required' => true,
                            'label' => 'Patient',
                            'options' => $users->pluck('first_name', 'user_id')
                        ],
                        'hospital_id' => [
                            'type' => 'select',
                            'required' => true,
                            'label' => 'Hospital',
                            'options' => $hospitals->pluck('name', 'hospital_id')
                        ],
                        'appointment_date' => [
                            'type' => 'date',
                            'required' => true,
                            'label' => 'Appointment Date'
                        ],
                        'appointment_time' => [
                            'type' => 'time',
                            'required' => true,
                            'label' => 'Appointment Time'
                        ],
                        'patient_phone' => [
                            'type' => 'text',
                            'required' => true,
                            'label' => 'Patient Phone'
                        ],
                        'symptom' => [
                            'type' => 'textarea',
                            'required' => false,
                            'label' => 'Symptoms'
                        ],
                        'status' => [
                            'type' => 'select',
                            'required' => true,
                            'label' => 'Status',
                            'options' => ['upcoming' => 'Upcoming', 'completed' => 'Completed', 'no_show' => 'No Show']
                        ]
                    ]
                ]
            ]);
        }
        
        return view('appointment_edit', compact('appointment', 'users', 'hospitals'));
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $updateType = $request->input('update_type', 'basic_info');

        switch ($updateType) {
            case 'basic_info':
                return $this->updateBasicInfo($request, $appointment);
            
            case 'status':
                return $this->updateStatus($request, $appointment);
            
            default:
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid update type'
                    ], 400);
                }
                return redirect()->route('appointment.edit', $appointment->appointment_id)->with('error', 'Invalid update type.');
        }
    }

    private function updateBasicInfo(Request $request, $appointment)
    {
        $request->validate([
            'user_id' => 'required|exists:users_table,user_id',
            'hospital_id' => 'required|exists:hospitals,hospital_id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'patient_phone' => 'required|string|max:20',
            'symptom' => 'nullable|string|max:500',
        ]);

        $appointment->update([
            'user_id' => $request->user_id,
            'hospital_id' => $request->hospital_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'patient_phone' => $request->patient_phone,
            'symptom' => $request->symptom,
        ]);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment information updated successfully!',
                'data' => $appointment->fresh()
            ]);
        }

        return redirect()->route('appointment.edit', $appointment->appointment_id)->with('success', 'Appointment information updated successfully!');
    }

    private function updateStatus(Request $request, $appointment)
    {
        $request->validate([
            'status' => 'required|in:upcoming,completed,no_show',
        ]);

        $appointment->update(['status' => $request->status]);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment status updated successfully!',
                'data' => $appointment->fresh()
            ]);
        }

        return redirect()->route('appointment.edit', $appointment->appointment_id)->with('success', 'Appointment status updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully!'
        ]);
    }
} 
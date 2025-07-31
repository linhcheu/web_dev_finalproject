<?php

namespace App\Http\Controllers\frontendController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\frontendModels\Appointment;
use App\Models\frontendModels\Hospital;
use App\Models\frontendModels\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Check if user is authenticated
     *
     * @param Request $request
     * @return User|JsonResponse The authenticated user or JSON error response
     */
    protected function authenticate(Request $request)
    {
        // Get the authenticated user from Sanctum middleware
        $sanctumUser = $request->user();
        
        if (!$sanctumUser) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'error' => 'Please provide valid login credentials'
            ], 401);
        }
        
        // Find the corresponding user in the frontend users table
        $user = User::where('user_id', $sanctumUser->user_id)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => 'User account not found in system'
            ], 404);
        }
        
        return $user;
    }

    /**
     * Display a listing of appointments.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Authenticate the user
        $user = $this->authenticate($request);
        if ($user instanceof JsonResponse) {
            return $user; // Return error response if authentication failed
        }

        // Get appointments for the authenticated user with related data
        $appointments = Appointment::where('user_id', $user->user_id)
            ->with(['hospital' => function($query) {
                $query->select('hospital_id', 'name', 'province', 'location', 'contact_info');
            }])
            ->orderBy('appointment_date', 'desc')
            ->get();
        
        // Transform appointments to include formatted dates and status labels
        $formattedAppointments = $appointments->map(function($appointment) {
            $statusLabels = [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'rescheduled' => 'Rescheduled'
            ];
            
            return [
                'id' => $appointment->appointment_id,
                'hospital' => [
                    'id' => $appointment->hospital->hospital_id,
                    'name' => $appointment->hospital->name,
                    'location' => $appointment->hospital->location,
                    'province' => $appointment->hospital->province,
                    'contact' => $appointment->hospital->contact_info
                ],
                'date' => [
                    'original' => $appointment->appointment_date,
                    'formatted' => $appointment->appointment_date ? Carbon::parse($appointment->appointment_date)->format('F j, Y') : null
                ],
                'time' => [
                    'original' => $appointment->appointment_time,
                    'formatted' => $appointment->appointment_time ? Carbon::parse($appointment->appointment_time)->format('g:i A') : null
                ],
                'reason' => $appointment->reason,
                'status' => [
                    'code' => $appointment->status,
                    'label' => $statusLabels[$appointment->status] ?? 'Unknown'
                ],
                'created_at' => [
                    'original' => $appointment->created_at,
                    'formatted' => $appointment->created_at->format('F j, Y g:i A')
                ],
                'actions' => [
                    'can_cancel' => in_array($appointment->status, ['pending', 'confirmed']),
                    'can_reschedule' => in_array($appointment->status, ['pending', 'confirmed']),
                    'can_view_receipt' => in_array($appointment->status, ['confirmed', 'completed'])
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'appointments' => $formattedAppointments,
                'count' => $formattedAppointments->count(),
                'user' => [
                    'id' => $user->user_id,
                    'name' => $user->first_name . ' ' . $user->last_name
                ]
            ]
        ]);
    }

    /**
     * Show the form for creating a new appointment.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        // Get hospitals for the appointment form
        $hospitals = Hospital::where('status', 'active')->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'hospitals' => $hospitals,
                'form_fields' => [
                    'hospital_id' => [
                        'type' => 'select',
                        'required' => true,
                        'label' => 'Hospital'
                    ],
                    'appointment_date' => [
                        'type' => 'date',
                        'required' => true,
                        'label' => 'Date'
                    ],
                    'appointment_time' => [
                        'type' => 'time',
                        'required' => true,
                        'label' => 'Time'
                    ],
                    'reason' => [
                        'type' => 'textarea',
                        'required' => true,
                        'label' => 'Reason for Visit'
                    ],
                    'notes' => [
                        'type' => 'textarea',
                        'required' => false,
                        'label' => 'Additional Notes'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Store a newly created appointment.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Authenticate the user
        $user = $this->authenticate($request);
        if ($user instanceof JsonResponse) {
            return $user; // Return error response if authentication failed
        }

        // Use Validator facade instead of $request->validate()
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'hospital_id' => 'required|exists:hospitals,hospital_id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'reason' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'error' => 'Authentication required'
            ], 401);
        }
        
        try {
            // Validate the request
            $validated = $request->validate([
                'hospital_id' => 'required|exists:hospitals,hospital_id', // Changed from hospitals_table to hospitals
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required',
                'reason' => 'required|string|max:500',
                'notes' => 'nullable|string|max:500'
            ]);
            
            // Create a new appointment
            $appointment = Appointment::create([
                'user_id' => $user->user_id,
                'hospital_id' => $validated['hospital_id'],
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'symptom' => $validated['reason'], // Map reason to symptom field
                'status' => 'upcoming'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'data' => [
                    'appointment' => $appointment
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create appointment', [
                'error' => $e->getMessage(),
                'user_id' => $user->user_id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment',
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Display the specified appointment.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        // Get the authenticated user
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'error' => 'Authentication required'
            ], 401);
        }

        try {
            // Find the appointment with related data
            $appointment = Appointment::with('hospital')
                ->where('user_id', $user->user_id)
                ->findOrFail($id);
            
            $statusLabels = [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'rescheduled' => 'Rescheduled'
            ];

            // Format the appointment details
            $formattedAppointment = [
                'id' => $appointment->appointment_id,
                'hospital' => [
                    'id' => $appointment->hospital->hospital_id,
                    'name' => $appointment->hospital->name,
                    'location' => $appointment->hospital->location,
                    'province' => $appointment->hospital->province,
                    'contact' => $appointment->hospital->contact_info
                ],
                'patient' => [
                    'id' => $user->user_id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone
                ],
                'date' => [
                    'original' => $appointment->appointment_date,
                    'formatted' => $appointment->appointment_date ? Carbon::parse($appointment->appointment_date)->format('F j, Y') : null
                ],
                'time' => [
                    'original' => $appointment->appointment_time,
                    'formatted' => $appointment->appointment_time ? Carbon::parse($appointment->appointment_time)->format('g:i A') : null
                ],
                'reason' => $appointment->reason ?? $appointment->symptoms,
                'status' => [
                    'code' => $appointment->status,
                    'label' => $statusLabels[$appointment->status] ?? 'Unknown'
                ],
                'created_at' => [
                    'original' => $appointment->created_at,
                    'formatted' => $appointment->created_at->format('F j, Y g:i A')
                ],
                'actions' => [
                    'can_cancel' => in_array($appointment->status, ['pending', 'confirmed']),
                    'can_reschedule' => in_array($appointment->status, ['pending', 'confirmed']),
                    'can_view_receipt' => in_array($appointment->status, ['confirmed', 'completed'])
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'appointment' => $formattedAppointment
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve appointment',
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Display appointment receipt.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function receipt(Request $request, $id): JsonResponse
    {
        // Get the authenticated user
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'error' => 'Authentication required'
            ], 401);
        }

        try {
            // Find the appointment
            $appointment = Appointment::with('hospital')
                ->where('user_id', $user->user_id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'receipt' => [
                        'appointment_id' => $appointment->appointment_id,
                        'patient' => [
                            'name' => $user->first_name . ' ' . $user->last_name,
                            'email' => $user->email,
                            'phone' => $user->phone
                        ],
                        'hospital' => [
                            'name' => $appointment->hospital->name,
                            'location' => $appointment->hospital->location,
                            'contact' => $appointment->hospital->contact_info
                        ],
                        'date' => [
                            'original' => $appointment->appointment_date,
                            'formatted' => $appointment->appointment_date ? Carbon::parse($appointment->appointment_date)->format('F j, Y') : null
                        ],
                        'time' => [
                            'original' => $appointment->appointment_time,
                            'formatted' => $appointment->appointment_time ? Carbon::parse($appointment->appointment_time)->format('g:i A') : null
                        ],
                        'status' => ucfirst($appointment->status),
                        'reason' => $appointment->reason ?? $appointment->symptoms,
                        'created_at' => $appointment->created_at->format('Y-m-d H:i:s'),
                        'reference_number' => 'APP-' . str_pad($appointment->appointment_id, 6, '0', STR_PAD_LEFT),
                        'print_url' => '/api/frontend/user/appointments/' . $appointment->appointment_id . '/print'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve appointment receipt',
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTrace() : null
            ], 500);
        }
    }
}

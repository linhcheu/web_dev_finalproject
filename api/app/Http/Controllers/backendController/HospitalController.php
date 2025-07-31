<?php

namespace App\Http\Controllers\backendController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\frontendModels\Hospital;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    /**
     * Display a listing of the hospitals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Ensure admin is authenticated via token
        $admin = $request->user();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }

        // Log for debugging
        Log::info('Admin accessing hospital list', [
            'admin_id' => $admin->admin_id,
            'token' => substr($request->bearerToken(), 0, 10) . '...'
        ]);

        // Get all hospitals with optional filtering
        $query = Hospital::query();
        
        // Apply filters if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('province', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        // Get paginated results
        $hospitals = $query->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $hospitals
        ]);
    }

    /**
     * Store a newly created hospital in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Ensure admin is authenticated via token
        $admin = $request->user();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'location' => 'required|string',
            'contact_info' => 'required|string',
            'information' => 'nullable|string',
            'owner_id' => 'nullable|exists:users_table,user_id',
            'status' => 'required|in:pending,active,rejected'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create the hospital
        $hospital = Hospital::create($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Hospital created successfully',
            'data' => $hospital
        ], 201);
    }

    /**
     * Display the specified hospital.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        // Ensure admin is authenticated via token
        $admin = $request->user();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Find the hospital
        $hospital = Hospital::with(['appointments', 'owner'])->find($id);
        
        if (!$hospital) {
            return response()->json([
                'success' => false,
                'message' => 'Hospital not found'
            ], 404);
        }
        
        // Get subscription info for the hospital
        $subscription = \App\Models\frontendModels\Subscription::where('hospital_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->first();
        
        // Count appointments
        $appointmentCounts = [
            'total' => $hospital->appointments->count(),
            'upcoming' => $hospital->appointments->where('status', 'upcoming')->count(),
            'completed' => $hospital->appointments->where('status', 'completed')->count(),
            'no_show' => $hospital->appointments->where('status', 'no_show')->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'hospital' => $hospital,
                'subscription' => $subscription,
                'appointment_counts' => $appointmentCounts,
            ]
        ]);
    }

    /**
     * Update the specified hospital in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Ensure admin is authenticated via token
        $admin = $request->user();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'province' => 'sometimes|required|string|max:100',
            'location' => 'sometimes|required|string',
            'contact_info' => 'sometimes|required|string',
            'information' => 'sometimes|nullable|string',
            'owner_id' => 'sometimes|nullable|exists:users_table,user_id',
            'status' => 'sometimes|required|in:pending,active,rejected'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Find the hospital
        $hospital = Hospital::find($id);
        
        if (!$hospital) {
            return response()->json([
                'success' => false,
                'message' => 'Hospital not found'
            ], 404);
        }
        
        // Update the hospital
        $hospital->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Hospital updated successfully',
            'data' => $hospital
        ]);
    }

    /**
     * Remove the specified hospital from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        // Ensure admin is authenticated via token
        $admin = $request->user();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Find the hospital
        $hospital = Hospital::find($id);
        
        if (!$hospital) {
            return response()->json([
                'success' => false,
                'message' => 'Hospital not found'
            ], 404);
        }
        
        // Delete the hospital
        $hospital->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Hospital deleted successfully'
        ]);
    }
    
    /**
     * Update hospital status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        // Ensure admin is authenticated via token
        $admin = $request->user();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,active,rejected',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Find the hospital
        $hospital = Hospital::find($id);
        
        if (!$hospital) {
            return response()->json([
                'success' => false,
                'message' => 'Hospital not found'
            ], 404);
        }
        
        // Update the status
        $hospital->update([
            'status' => $request->status
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Hospital status updated successfully',
            'data' => $hospital
        ]);
    }

    /**
     * Show the form for editing the specified hospital.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id)
    {
        // Ensure admin is authenticated via token
        $admin = $request->user();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Find the hospital
        $hospital = Hospital::find($id);
        
        if (!$hospital) {
            return response()->json([
                'success' => false,
                'message' => 'Hospital not found'
            ], 404);
        }
        
        // Return the form structure and current values
        return response()->json([
            'success' => true,
            'data' => [
                'hospital' => $hospital,
                'form_fields' => [
                    'name' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Hospital Name',
                        'value' => $hospital->name
                    ],
                    'province' => [
                        'type' => 'select',
                        'required' => true,
                        'label' => 'Province',
                        'value' => $hospital->province,
                        'options' => [
                            'Phnom Penh', 'Banteay Meanchey', 'Battambang', 'Kampong Cham',
                            'Kampong Chhnang', 'Kampong Speu', 'Kampong Thom', 'Kampot',
                            'Kandal', 'Kep', 'Koh Kong', 'Kratie', 'Mondulkiri',
                            'Oddar Meanchey', 'Pailin', 'Preah Vihear', 'Prey Veng',
                            'Pursat', 'Ratanakiri', 'Siem Reap', 'Sihanoukville',
                            'Stung Treng', 'Svay Rieng', 'Takeo', 'Tbong Khmum'
                        ]
                    ],
                    'location' => [
                        'type' => 'textarea',
                        'required' => true,
                        'label' => 'Location',
                        'value' => $hospital->location
                    ],
                    'contact_info' => [
                        'type' => 'textarea',
                        'required' => true,
                        'label' => 'Contact Information',
                        'value' => $hospital->contact_info
                    ],
                    'information' => [
                        'type' => 'textarea',
                        'required' => false,
                        'label' => 'Additional Information',
                        'value' => $hospital->information
                    ],
                    'status' => [
                        'type' => 'select',
                        'required' => true,
                        'label' => 'Status',
                        'value' => $hospital->status,
                        'options' => [
                            'pending' => 'Pending',
                            'active' => 'Active',
                            'rejected' => 'Rejected'
                        ]
                    ]
                ],
                'submission' => [
                    'url' => "/api/backend/hospital/{$id}",
                    'method' => 'PUT'
                ]
            ]
        ]);
    }
}
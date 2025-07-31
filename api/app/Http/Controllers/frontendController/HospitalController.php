<?php

namespace App\Http\Controllers\frontendController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\frontendModels\Hospital;
use App\Models\frontendModels\User;
use App\Models\frontendModels\Appointment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class HospitalController extends Controller
{
    /**
     * Verify if user is authenticated and has hospital admin role
     * 
     * @param Request $request
     * @return array|false Returns user data if authenticated or false otherwise
     */
    private function verifyHospitalAdmin(Request $request)
    {
        // Get the authenticated user from Sanctum middleware
        $user = $request->user();
        
        // Debug logging to track authentication flow
        Log::info('Hospital admin verification attempt', [
            'has_token_user' => $user ? true : false,
            'user_id' => $user ? $user->user_id : null,
            'user_role' => $user ? $user->role : null,
            'token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null
        ]);
        
        // If no authenticated user from middleware, authentication failed
        if (!$user) {
            Log::warning('Hospital admin verification failed: no authenticated user from Sanctum middleware');
            return false;
        }
        
        // Verify the user has hospital_admin role
        if ($user->role !== 'hospital_admin') {
            Log::warning('Hospital admin verification failed: user does not have hospital_admin role', [
                'user_id' => $user->user_id,
                'actual_role' => $user->role
            ]);
            return false;
        }
        
        // Find the hospital this admin manages
        $hospital = Hospital::where('owner_id', $user->user_id)->first();
        
        if (!$hospital) {
            Log::warning('Hospital admin verification failed: no hospital found for user', [
                'user_id' => $user->user_id,
                'role' => $user->role
            ]);
            return false;
        }
        
        Log::info('Hospital admin verified successfully', [
            'user_id' => $user->user_id,
            'hospital_id' => $hospital->hospital_id,
            'hospital_name' => $hospital->name
        ]);
        
        return [
            'user' => $user,
            'hospital' => $hospital
        ];
    }
    
    // Public methods - No auth required
    
    /**
     * Display a listing of hospitals
     */
    public function index(Request $request)
    {
        $query = Hospital::where('status', 'active');
        
        // Filter by province if provided
        if ($request->has('province') && $request->province) {
            $query->where('province', $request->province);
        }
        
        // Search by name if provided
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $hospitals = $query->paginate(10);
        
        // Get unique provinces for filter dropdown
        $provinces = Hospital::distinct()->pluck('province')->filter()->values();
        
        return response()->json([
            'success' => true,
            'data' => [
                'hospitals' => $hospitals,
                'provinces' => $provinces
            ]
        ]);
    }
    
    /**
     * Display the specified hospital with basic info
     */
    public function show(Request $request, $hospitalId)
    {
        // Find the hospital
        $hospital = \App\Models\frontendModels\Hospital::findOrFail($hospitalId);
        
        // Get the authenticated user
        $user = $request->auth_user;
        
        // Base hospital data that everyone can see
        $hospitalData = [
            'hospital_id' => $hospital->hospital_id,
            'name' => $hospital->name,
            'province' => $hospital->province,
            'location' => $hospital->location,
            'contact_info' => $hospital->contact_info,
            'information' => $hospital->information,
            'profile_picture' => $hospital->profile_picture,
            'status' => $hospital->status,
        ];
        
        // Additional data for authorized users
        $additionalData = [];
        
        // If user is a hospital admin for this hospital, include admin-specific data
        if ($user && $user->role === 'hospital_admin' && $hospital->owner_id === $user->user_id) {
            $subscription = \App\Models\frontendModels\Subscription::where('hospital_id', $hospitalId)
                ->orderBy('created_at', 'desc')
                ->first();
                
            $appointments = \App\Models\frontendModels\Appointment::where('hospital_id', $hospitalId)
                ->count();
                
            $additionalData = [
                'is_owner' => true,
                'subscription' => $subscription,
                'appointments_count' => $appointments,
                'active_since' => $hospital->created_at->format('M d, Y')
            ];
        }
        // For regular users, include user-specific data
        else if ($user && $user->role === 'user') {
            $hasAppointment = \App\Models\frontendModels\Appointment::where('hospital_id', $hospitalId)
                ->where('user_id', $user->user_id)
                ->exists();
                
            $additionalData = [
                'has_appointment' => $hasAppointment,
                'can_book' => true
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => array_merge($hospitalData, $additionalData)
        ]);
    }
    
    // Hospital admin methods - Auth required
    
    /**
     * Display dashboard data for hospital admin
     */
    public function dashboard(Request $request)
    {
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.'
            ], 401);
        }
        
        $hospital = $auth['hospital'];
        $hospitalId = $hospital->hospital_id;
        
        // Count stats for dashboard
        $totalAppointments = Appointment::where('hospital_id', $hospitalId)->count();
        $upcomingAppointments = Appointment::where('hospital_id', $hospitalId)
                            ->where('status', 'upcoming')->count();
        $completedAppointments = Appointment::where('hospital_id', $hospitalId)
                            ->where('status', 'completed')->count();
        $todayAppointments = Appointment::where('hospital_id', $hospitalId)
                            ->whereDate('appointment_date', date('Y-m-d'))->count();
        
        // Get recent appointments
        $recentAppointments = Appointment::with('user')
                            ->where('hospital_id', $hospitalId)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'hospital' => $hospital,
                'stats' => [
                    'total_appointments' => $totalAppointments,
                    'upcoming_appointments' => $upcomingAppointments,
                    'completed_appointments' => $completedAppointments,
                    'today_appointments' => $todayAppointments
                ],
                'recent_appointments' => $recentAppointments
            ]
        ]);
    }
    
    /**
     * Display appointments for hospital admin
     */
    public function appointments(Request $request)
    {
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.'
            ], 401);
        }
        
        $hospital = $auth['hospital'];
        $hospitalId = $hospital->hospital_id;
        
        // Build query for appointments
        $query = Appointment::with('user')
                ->where('hospital_id', $hospitalId);
        
        // Filter by status if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Filter by date if provided
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('appointment_date', $request->date);
        }
        
        // Search by patient name if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }
        
        // Sort results
        $sortField = $request->input('sort_field', 'appointment_date');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        // Paginate results
        $perPage = $request->input('per_page', 10);
        $appointments = $query->paginate($perPage);
        
        // Get status counts for filter badges
        $statusCounts = [
            'all' => Appointment::where('hospital_id', $hospitalId)->count(),
            'upcoming' => Appointment::where('hospital_id', $hospitalId)->where('status', 'upcoming')->count(),
            'completed' => Appointment::where('hospital_id', $hospitalId)->where('status', 'completed')->count(),
            'no_show' => Appointment::where('hospital_id', $hospitalId)->where('status', 'no_show')->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'appointments' => $appointments,
                'status_counts' => $statusCounts,
                'hospital' => [
                    'id' => $hospital->hospital_id,
                    'name' => $hospital->name
                ]
            ]
        ]);
    }
    
    /**
     * Update appointment status
     */
    public function updateAppointment(Request $request, $appointmentId)
    {
        // Add detailed logging for debugging token auth issues
        Log::info('Update appointment request received', [
            'appointment_id' => $appointmentId,
            'has_token' => $request->bearerToken() ? true : false,
            'token_prefix' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null,
            'user' => $request->user() ? [
                'id' => $request->user()->user_id,
                'role' => $request->user()->role
            ] : null
        ]);
        
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            // Enhanced error message with debugging info
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.',
                'debug' => [
                    'has_user' => $request->user() ? true : false,
                    'user_role' => $request->user() ? $request->user()->role : null,
                    'has_token' => $request->bearerToken() ? true : false
                ]
            ], 401);
        }
        
        $hospital = $auth['hospital'];
        $hospitalId = $hospital->hospital_id;
        
        // Validate request - only need status now
        $validated = $request->validate([
            'status' => 'required|in:upcoming,completed,no_show'
        ]);
        
        // Find appointment and verify it belongs to this hospital
        $appointment = Appointment::where('appointment_id', $appointmentId)
                        ->where('hospital_id', $hospitalId)
                        ->first();

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found or not associated with your hospital.'
            ], 404);
        }
        
        // Update appointment
        $appointment->update([
            'status' => $validated['status']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully',
            'data' => $appointment
        ]);
    }
    
    /**
     * Display hospital profile
     */
    public function profile(Request $request)
    {
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.'
            ], 401);
        }
        
        $user = $auth['user'];
        $hospital = $auth['hospital'];
        
        // Get the hospital data with any related information
        $hospitalData = [
            'hospital_id' => $hospital->hospital_id,
            'hospital_name' => $hospital->name,
            'province' => $hospital->province,
            'location' => $hospital->location,
            'contact_info' => $hospital->contact_info,
            'information' => $hospital->information,
            'profile_picture' => $hospital->profile_picture,
            'status' => $hospital->status,
            'created_at' => $hospital->created_at,
            'updated_at' => $hospital->updated_at
        ];
        
        // Get admin profile data
        $adminData = [
            'user_id' => $user->user_id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'profile_picture' => $user->profile_picture
        ];
        
        // Get subscription info if available
        $subscription = \App\Models\frontendModels\Subscription::where('hospital_id', $hospital->hospital_id)
                        ->where('status', 'active')
                        ->first();
    
        $subscriptionData = $subscription ? [
            'subscription_id' => $subscription->subscription_id,
            'plan_type' => $subscription->plan_type,
            'price' => $subscription->price,
            'start_date' => $subscription->start_date,
            'end_date' => $subscription->end_date,
            'status' => $subscription->status,
            'auto_renew' => $subscription->auto_renew
        ] : null;
        
        return response()->json([
            'success' => true,
            'data' => [
                'hospital' => $hospitalData,
                'admin' => $adminData,
                'subscription' => $subscriptionData
            ]
        ]);
    }
    
    /**
     * Update hospital profile
     */
    public function updateProfile(Request $request)
    {
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.'
            ], 401);
        }
        
        $user = $auth['user'];
        $hospital = $auth['hospital'];
        
        // Validate request
        $validated = $request->validate([
            'hospital_name' => 'sometimes|required|string|max:100',
            'province' => 'sometimes|required|string|max:100',
            'location' => 'sometimes|required|string',
            'contact_info' => 'sometimes|required|string',
            'information' => 'sometimes|nullable|string',
            
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:users_table,email,'.$user->user_id.',user_id',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);
        
        // Update hospital info
        $hospitalData = [];
        if (isset($validated['hospital_name'])) $hospitalData['name'] = $validated['hospital_name'];
        if (isset($validated['province'])) $hospitalData['province'] = $validated['province'];
        if (isset($validated['location'])) $hospitalData['location'] = $validated['location'];
        if (isset($validated['contact_info'])) $hospitalData['contact_info'] = $validated['contact_info'];
        if (isset($validated['information'])) $hospitalData['information'] = $validated['information'];
        
        if (!empty($hospitalData)) {
            $hospital->update($hospitalData);
        }
        
        // Update admin info
        $userData = [];
        if (isset($validated['first_name'])) $userData['first_name'] = $validated['first_name'];
        if (isset($validated['last_name'])) $userData['last_name'] = $validated['last_name'];
        if (isset($validated['email'])) $userData['email'] = $validated['email'];
        if (isset($validated['phone'])) $userData['phone'] = $validated['phone'];
        
        if (!empty($userData)) {
            $user->update($userData);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated suxccessfully',
            'data' => [
                'hospital' => $hospital,
                'admin' => $user
            ]
        ]);
    }
    
    /**
     * Get subscription information for the hospital
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscription(Request $request)
    {
        Log::info('Subscription request received', [
            'headers' => $request->header(),
            'has_user' => $request->user() ? true : false,
            'token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null
        ]);
        
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.',
                'auth_method' => $request->user() ? 'token' : 'basic'
            ], 401);
        }
        
        $hospital = $auth['hospital'];
        
        // Get active subscription
        $subscription = \App\Models\frontendModels\Subscription::where('hospital_id', $hospital->hospital_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
    
        // Get subscription plans info
        $plans = [
            'basic' => [
                'name' => 'Basic Plan',
                'price' => 9.99,
                'features' => [
                    'Up to 50 appointments per month',
                    'Basic analytics',
                    'Email support'
                ]
            ],
            'premium' => [
                'name' => 'Premium Plan',
                'price' => 29.99,
                'features' => [
                    'Unlimited appointments',
                    'Advanced analytics',
                    'Priority email & phone support',
                    'Custom hospital profile'
                ]
            ],
            'enterprise' => [
                'name' => 'Enterprise Plan',
                'price' => 99.99,
                'features' => [
                    'Unlimited appointments',
                    'Premium analytics with insights',
                    '24/7 dedicated support',
                    'Custom hospital profile',
                    'Multiple administrator accounts'
                ]
            ]
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'subscription' => $subscription,
                'plans' => $plans,
                'hospital' => [
                    'id' => $hospital->hospital_id,
                    'name' => $hospital->name
                ]
            ]
        ]);
    }
    
    /**
     * Update hospital subscription
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSubscription(Request $request)
    {
        Log::info('Update subscription request received', [
            'headers' => $request->header(),
            'has_user' => $request->user() ? true : false,
            'token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null,
            'request_data' => $request->all()
        ]);
        
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.',
                'auth_method' => $request->user() ? 'token' : 'basic'
            ], 401);
        }
        
        $hospital = $auth['hospital'];
        
        // Validate request
        $request->validate([
            'plan_type' => 'required|in:basic,premium,enterprise',
            'auto_renew' => 'sometimes|boolean'
        ]);
        
        // Get plan pricing
        $prices = [
            'basic' => 9.99,
            'premium' => 29.99,
            'enterprise' => 99.99
        ];
        
        // Check for existing subscription
        $existingSubscription = \App\Models\frontendModels\Subscription::where('hospital_id', $hospital->hospital_id)
                            ->where('status', 'active')
                            ->first();
    
        if ($existingSubscription) {
            // Update existing subscription
            $existingSubscription->update([
                'plan_type' => $request->plan_type,
                'price' => $prices[$request->plan_type],
                'auto_renew' => $request->has('auto_renew') ? $request->auto_renew : $existingSubscription->auto_renew,
                'start_date' => now(),
                'end_date' => now()->addMonth()
            ]);
            
            $subscription = $existingSubscription;
            $message = 'Subscription updated successfully';
        } else {
            // Create new subscription
            $subscription = \App\Models\frontendModels\Subscription::create([
                'hospital_id' => $hospital->hospital_id,
                'plan_type' => $request->plan_type,
                'price' => $prices[$request->plan_type],
                'auto_renew' => $request->has('auto_renew') ? $request->auto_renew : true,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addMonth()
            ]);
            
            $message = 'Subscription created successfully';
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $subscription
        ]);
    }
    
    /**
     * Cancel hospital subscription
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelSubscription(Request $request)
    {
        Log::info('Cancel subscription request received', [
            'headers' => $request->header(),
            'has_user' => $request->user() ? true : false,
            'token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null
        ]);
        
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.',
                'auth_method' => $request->user() ? 'token' : 'basic'
            ], 401);
        }
        
        $hospital = $auth['hospital'];
        
        // Find active subscription
        $subscription = \App\Models\frontendModels\Subscription::where('hospital_id', $hospital->hospital_id)
                    ->where('status', 'active')
                    ->first();
    
        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found'
            ], 404);
        }
        
        // Cancel subscription
        $subscription->update([
            'status' => 'cancelled',
            'auto_renew' => false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully',
            'data' => $subscription
        ]);
    }

    /**
     * Get feedback form structure for hospital
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function hospitalFeedback(Request $request)
    {
        Log::info('Hospital feedback form request received', [
            'headers' => $request->header(),
            'has_user' => $request->user() ? true : false,
            'token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null
        ]);
        
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.',
                'auth_method' => $request->user() ? 'token' : 'basic'
            ], 401);
        }
        
        $hospital = $auth['hospital'];
        
        // Return the form structure in JSON format that matches the actual database schema
        return response()->json([
            'success' => true,
            'data' => [
                'hospital' => [
                    'id' => $hospital->hospital_id,
                    'name' => $hospital->name
                ],
                'form_fields' => [
                    'comments' => [
                        'type' => 'textarea',
                        'required' => true,
                        'label' => 'Your Feedback',
                        'placeholder' => 'Please share your feedback about the hospital...',
                        'maxlength' => 500
                    ]
                    // Removed rating and is_public fields as they don't exist in the database
                ],
                'validation_rules' => [
                    'comments' => 'required|string|max:500'
                    // Removed validation rules for non-existent fields
                ],
                'submit_endpoint' => '/api/frontend/hospital/feedback',
                'method' => 'POST',
                'note' => 'Form structure matches the database schema defined in migration file'
            ]
        ]);
    }

    /**
     * Get all feedback for this hospital (new method)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHospitalFeedback(Request $request)
    {
        Log::info('Get hospital feedback data request received', [
            'headers' => $request->header(),
            'has_user' => $request->user() ? true : false,
            'token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null
        ]);
        
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.',
                'auth_method' => $request->user() ? 'token' : 'basic'
            ], 401);
        }
        
        $hospital = $auth['hospital'];
        $hospitalId = $hospital->hospital_id;
        
        // First get all users who have appointments with this hospital
        $userIds = Appointment::where('appointments.hospital_id', $hospitalId)
            ->distinct()
            ->pluck('user_id')
            ->toArray();
    
        // Then get all feedback from those users
        $feedback = \App\Models\frontendModels\Feedback::whereIn('feedback.user_id', $userIds)
        ->join('users_table', 'feedback.user_id', '=', 'users_table.user_id')
        ->select(
            'feedback.*',
            'users_table.first_name',
            'users_table.last_name',
            'users_table.email'
        )
        ->orderBy('feedback.created_at', 'desc')
        ->get();
    
        // Get recent appointments for context
        $recentAppointments = Appointment::whereIn('appointments.user_id', array_slice($userIds, 0, 20))
        ->where('appointments.hospital_id', $hospitalId)
        ->orderBy('appointments.created_at', 'desc')
        ->limit(20)
        ->get(['appointment_id', 'user_id', 'appointment_date', 'status']);
    
        return response()->json([
            'success' => true,
            'data' => [
                'feedback' => $feedback,
                'hospital' => [
                    'id' => $hospitalId,
                    'name' => $hospital->name
                ],
                'total_feedback_count' => $feedback->count(),
                'recent_appointments' => $recentAppointments,
                'note' => 'Shows feedback from all users who have appointments with your hospital'
            ]
        ]);
    }

    /**
     * Submit feedback for a hospital
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitHospitalFeedback(Request $request)
    {
        Log::info('Submit hospital feedback request received', [
            'headers' => $request->header(),
            'has_user' => $request->user() ? true : false,
            'token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null,
            'request_data' => $request->all()
        ]);
        
        // Verify hospital admin authentication
        $auth = $this->verifyHospitalAdmin($request);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hospital admin access required.',
                'auth_method' => $request->user() ? 'token' : 'basic'
            ], 401);
        }
        
        $hospital = $auth['hospital'];
        $user = $auth['user'];
        
        // Validate request - only validate the fields that exist in the database
        $validated = $request->validate([
            'comments' => 'required|string|max:500',
            // Removed validation for non-existent fields
        ]);
        
        // Create feedback with only the fields that exist in the database
        $feedback = \App\Models\frontendModels\Feedback::create([
            'user_id' => $user->user_id,
            'comments' => $validated['comments']
            // No rating or is_public fields
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'data' => $feedback
        ]);
    }
}

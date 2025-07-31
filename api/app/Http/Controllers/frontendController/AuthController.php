<?php

namespace App\Http\Controllers\frontendController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\frontendModels\User;
use App\Models\frontendModels\Hospital;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show login form (API version returns form structure)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showLogin(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'form_fields' => [
                    'email' => [
                        'type' => 'email',
                        'required' => true,
                        'label' => 'Email Address'
                    ],
                    'password' => [
                        'type' => 'password',
                        'required' => true,
                        'label' => 'Password'
                    ],
                    'remember' => [
                        'type' => 'checkbox',
                        'required' => false,
                        'label' => 'Remember Me'
                    ]
                ],
                'validation_rules' => [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            ]
        ]);
    }

    /**
     * Process login request - supports both JSON and Basic Auth
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Check for Basic Auth header (used in Insomnia Auth tab)
        $email = null;
        $password = null;
        
        // Extract credentials from Basic Auth header
        if ($request->header('Authorization')) {
            $header = $request->header('Authorization');
            if (strpos($header, 'Basic ') === 0) {
                $base64Credentials = substr($header, 6);
                $credentials = base64_decode($base64Credentials);
                if (strpos($credentials, ':') !== false) {
                    list($email, $password) = explode(':', $credentials, 2);
                }
            }
        }
        
        // If not using Basic Auth, get from request body
        if (!$email || !$password) {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            
            $email = $validated['email'];
            $password = $validated['password'];
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => ['email' => ['The provided credentials are incorrect.']]
            ], 401);
        }

        // Revoke existing tokens
        $user->tokens()->delete();

        // Determine the ability based on user role
        $ability = $user->role === 'hospital_admin' ? 'hospital' : 'user';
        
        // Create a token with the appropriate ability
        $token = $user->createToken('auth-token', [$ability])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->user_id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'token' => $token,
                'token_type' => 'Bearer',
                'usage_hint' => 'Use this token in the Authorization header as: Bearer YOUR_TOKEN'
            ]
        ]);
    }

    /**
     * Show register form (API version returns form structure)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showRegister(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'form_fields' => [
                    'first_name' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'First Name'
                    ],
                    'last_name' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Last Name'
                    ],
                    'email' => [
                        'type' => 'email',
                        'required' => true,
                        'label' => 'Email Address'
                    ],
                    'password' => [
                        'type' => 'password',
                        'required' => true,
                        'label' => 'Password'
                    ],
                    'password_confirmation' => [
                        'type' => 'password',
                        'required' => true,
                        'label' => 'Confirm Password'
                    ],
                    'phone' => [
                        'type' => 'text',
                        'required' => false,
                        'label' => 'Phone Number'
                    ]
                ],
                'validation_rules' => [
                    'first_name' => 'required|string|max:100',
                    'last_name' => 'required|string|max:100',
                    'email' => 'required|email|unique:users_table,email',
                    'password' => 'required|string|min:8|confirmed',
                    'phone' => 'nullable|string|max:20'
                ]
            ]
        ]);
    }

    /**
     * Process registration request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        // Get raw content and try to decode it
        $rawContent = $request->getContent();
        
        // Try to fix common JSON errors (like trailing commas)
        $fixedJson = preg_replace('/,\s*([\]}])/m', '$1', $rawContent);
        $jsonData = json_decode($fixedJson, true);
        
        // Log for debugging
        Log::info('Register Request Data', [
            'raw' => $rawContent,
            'fixed' => $fixedJson,
            'all' => $request->all(),
            'jsonData' => $jsonData,
            'headers' => $request->header()
        ]);

        // Check for direct input parameters
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $password = $request->input('password');
        $password_confirmation = $request->input('password_confirmation');
        $phone = $request->input('phone');
        
        // If not found in direct input, try from JSON
        if (empty($first_name) && isset($jsonData['first_name'])) {
            $first_name = $jsonData['first_name'];
        }
        if (empty($last_name) && isset($jsonData['last_name'])) {
            $last_name = $jsonData['last_name'];
        }
        if (empty($email) && isset($jsonData['email'])) {
            $email = $jsonData['email'];
        }
        if (empty($password) && isset($jsonData['password'])) {
            $password = $jsonData['password'];
        }
        if (empty($password_confirmation) && isset($jsonData['password_confirmation'])) {
            $password_confirmation = $jsonData['password_confirmation'];
        }
        if (empty($phone) && isset($jsonData['phone'])) {
            $phone = $jsonData['phone'];
        }
        
        // Create data array manually
        $data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
            'phone' => $phone
        ];
        
        // Manually validate
        if (empty($data['email']) || empty($data['first_name']) || empty($data['last_name']) || empty($data['password'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid JSON format or missing required fields',
                'debug' => [
                    'raw_content' => $rawContent,
                    'suggestion' => 'Please check your JSON syntax. Remove any trailing commas. Make sure all required fields are provided.',
                    'example' => '{"first_name":"John","last_name":"Doe","email":"john@example.com","password":"password123","password_confirmation":"password123","phone":"1234567890"}'
                ]
            ], 400);
        }
        
        // Validate
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users_table,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'debug' => [
                    'data_received' => array_merge(
                        array_filter($data, function($key) { return $key !== 'password' && $key !== 'password_confirmation'; }, ARRAY_FILTER_USE_KEY),
                        ['password' => '(hidden for security)', 'password_confirmation' => '(hidden for security)']
                    ),
                    'raw_content_length' => strlen($rawContent)
                ]
            ], 422);
        }
        
        // Create new user
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => 'user',
            'is_active' => true
        ]);
        
        // Generate a proper Sanctum token instead of random bytes
        $ability = 'user';
        $token = $user->createToken('registration-token', [$ability])->plainTextToken;
        
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->user_id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'token' => $token,
                'token_type' => 'Bearer',
                'usage_hint' => 'Use this token in the Authorization header as: Bearer YOUR_TOKEN'
            ]
        ], 201);
    }

    /**
     * Show hospital registration form.
     * For API requests, we return the form structure rather than a view.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showHospitalRegister()
    {
        // For API requests, return form fields and validation rules including subscription plans
        return response()->json([
            'success' => true,
            'data' => [
                'form_fields' => [
                    'first_name' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'First Name'
                    ],
                    'last_name' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Last Name'
                    ],
                    'email' => [
                        'type' => 'email',
                        'required' => true,
                        'label' => 'Email'
                    ],
                    'password' => [
                        'type' => 'password',
                        'required' => true,
                        'label' => 'Password'
                    ],
                    'password_confirmation' => [
                        'type' => 'password',
                        'required' => true,
                        'label' => 'Confirm Password'
                    ],
                    'phone' => [
                        'type' => 'text',
                        'required' => false,
                        'label' => 'Phone Number'
                    ],
                    'hospital_name' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Hospital Name'
                    ],
                    'hospital_province' => [
                        'type' => 'select',
                        'required' => true,
                        'label' => 'Province',
                        'options' => [
                            'Phnom Penh', 'Banteay Meanchey', 'Battambang', 'Kampong Cham',
                            'Kampong Chhnang', 'Kampong Speu', 'Kampong Thom', 'Kampot',
                            'Kandal', 'Kep', 'Koh Kong', 'Kratie', 'Mondulkiri',
                            'Oddar Meanchey', 'Pailin', 'Preah Vihear', 'Prey Veng',
                            'Pursat', 'Ratanakiri', 'Siem Reap', 'Sihanoukville',
                            'Stung Treng', 'Svay Rieng', 'Takeo', 'Tbong Khmum'
                        ]
                    ],
                    'hospital_location' => [
                        'type' => 'textarea',
                        'required' => true,
                        'label' => 'Location'
                    ],
                    'hospital_contact' => [
                        'type' => 'textarea',
                        'required' => true,
                        'label' => 'Contact Information'
                    ],
                    'create_subscription' => [
                        'type' => 'checkbox',
                        'required' => false,
                        'label' => 'Create subscription for this hospital'
                    ],
                    'plan_type' => [
                        'type' => 'select',
                        'required' => false,
                        'label' => 'Subscription Plan',
                        'options' => [
                            'basic' => 'Basic Plan ($9.99/month)',
                            'premium' => 'Premium Plan ($29.99/month)',
                            'enterprise' => 'Enterprise Plan ($99.99/month)'
                        ],
                        'depends_on' => 'create_subscription'
                    ],
                    'payment_method' => [
                        'type' => 'select',
                        'required' => false,
                        'label' => 'Payment Method',
                        'options' => [
                            'credit_card' => 'Credit Card',
                            'paypal' => 'PayPal',
                            'bank_transfer' => 'Bank Transfer'
                        ],
                        'depends_on' => 'create_subscription'
                    ],
                    'card_number' => [
                        'type' => 'text',
                        'required' => false,
                        'label' => 'Card Number',
                        'depends_on' => 'payment_method',
                        'depends_value' => 'credit_card'
                    ],
                    'card_holder' => [
                        'type' => 'text',
                        'required' => false,
                        'label' => 'Card Holder Name',
                        'depends_on' => 'payment_method',
                        'depends_value' => 'credit_card'
                    ],
                    'expiration_date' => [
                        'type' => 'text',
                        'required' => false,
                        'label' => 'Expiration Date (MM/YY)',
                        'depends_on' => 'payment_method',
                        'depends_value' => 'credit_card'
                    ],
                    'cvv' => [
                        'type' => 'text',
                        'required' => false,
                        'label' => 'CVV',
                        'depends_on' => 'payment_method',
                        'depends_value' => 'credit_card'
                    ]
                ],
                'subscription_plans' => [
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
                            'Multiple administrator accounts',
                            'API access'
                        ]
                    ]
                ],
                'validation_rules' => [
                    'first_name' => 'required|string|max:100',
                    'last_name' => 'required|string|max:100',
                    'email' => 'required|email|unique:users_table,email',
                    'password' => 'required|string|min:8|confirmed',
                    'phone' => 'nullable|string|max:20',
                    'hospital_name' => 'required|string|max:100',
                    'hospital_province' => 'required|string|max:100',
                    'hospital_location' => 'required|string',
                    'hospital_contact' => 'required|string',
                    'create_subscription' => 'boolean',
                    'plan_type' => 'required_if:create_subscription,1|in:basic,premium,enterprise',
                    'payment_method' => 'required_if:create_subscription,1|in:credit_card,paypal,bank_transfer',
                    'card_number' => 'required_if:payment_method,credit_card',
                    'card_holder' => 'required_if:payment_method,credit_card',
                    'expiration_date' => 'required_if:payment_method,credit_card',
                    'cvv' => 'required_if:payment_method,credit_card'
                ]
            ]
        ]);
    }
    
    /**
     * Register a new hospital admin with subscription if requested.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function hospitalRegister(Request $request)
    {
        // Validate the request - basic validation first
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users_table,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'hospital_name' => 'required|string|max:100',
            'hospital_province' => 'required|string|max:100',
            'hospital_location' => 'required|string',
            'hospital_contact' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Additional validation if subscription is requested
        if ($request->create_subscription) {
            $subscriptionValidator = Validator::make($request->all(), [
                'plan_type' => 'required|in:basic,premium,enterprise',
                'payment_method' => 'required|in:credit_card,paypal,bank_transfer',
            ]);
            
            // Card validation if credit card is selected
            if ($request->payment_method == 'credit_card') {
                $cardValidator = Validator::make($request->all(), [
                    'card_number' => 'required|string',
                    'card_holder' => 'required|string',
                    'expiration_date' => 'required|string',
                    'cvv' => 'required|string',
                ]);
                
                if ($cardValidator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment validation error',
                        'errors' => $cardValidator->errors()
                    ], 422);
                }
            }
            
            if ($subscriptionValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription validation error',
                    'errors' => $subscriptionValidator->errors()
                ], 422);
            }
        }
        
        // Create the hospital admin user
        $user = \App\Models\frontendModels\User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'hospital_admin',
            'is_active' => true
        ]);
        
        // Create the hospital
        $hospital = \App\Models\frontendModels\Hospital::create([
            'name' => $request->hospital_name,
            'province' => $request->hospital_province,
            'location' => $request->hospital_location,
            'contact_info' => $request->hospital_contact,
            'owner_id' => $user->user_id,
            'status' => 'pending' // New hospitals are pending until approved
        ]);
        
        // Create subscription if requested
        $subscription = null;
        $payment = null;
        
        if ($request->create_subscription) {
            // Get price based on plan type
            $prices = [
                'basic' => 9.99,
                'premium' => 29.99,
                'enterprise' => 99.99
            ];
            
            $price = $prices[$request->plan_type] ?? 9.99;
            
            // Create subscription
            $subscription = \App\Models\frontendModels\Subscription::create([
                'hospital_id' => $hospital->hospital_id,
                'plan_type' => $request->plan_type,
                'price' => $price,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'status' => 'active',
                'auto_renew' => true
            ]);
            
            // Process payment (simulated)
            // In a real app, you would integrate with a payment gateway here
            $payment = [
                'transaction_id' => 'TXN_' . substr(md5(mt_rand()), 0, 10),
                'amount' => $price,
                'method' => $request->payment_method,
                'status' => 'completed',
                'timestamp' => now()->toIso8601String()
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => $request->create_subscription 
                ? 'Hospital administrator registered successfully with active subscription.'
                : 'Hospital administrator registered successfully. Your account is pending approval.',
            'data' => [
                'user' => $user,
                'hospital' => $hospital,
                'subscription' => $subscription,
                'payment' => $payment
            ]
        ], 201);
    }
    
    public function profile()
    {
        return response()->json(['view' => 'profile']);
    }
    
    /**
     * Update the user's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        // First try to get user from token authentication
        $user = $request->user();
        
        // If token auth failed, try manual authentication
        if (!$user) {
            $user = $this->authenticate($request);
            if ($user instanceof JsonResponse) {
                return $user; // Return error response if authentication failed
            }
        }
        
        // Validate request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update the user profile
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone
        ]);
        
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->update(['profile_picture' => $path]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'redirect_path' => '/profile',
            'user' => [
                'id' => $user->user_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'profile_picture' => $user->profile_picture
            ]
        ]);
    }

    public function redirectToGoogle()
    {
        $redirectUrl = Socialite::driver('google')->redirect()->getTargetUrl();
        
        return response()->json([
            'redirect_url' => $redirectUrl
        ]);
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = \App\Models\User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'first_name' => $googleUser->user['given_name'] ?? '',
                'last_name' => $googleUser->user['family_name'] ?? '',
                'email' => $googleUser->getEmail(),
                'profile_picture' => $googleUser->getAvatar(),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        Auth::login($user, true);

        return response()->json([
            'success' => true,
            'redirect_path' => '/'
        ]);
    }

    /**
     * Check if user is authenticated
     *
     * @param Request $request
     * @return User|JsonResponse The authenticated user or JSON error response
     */
    protected function authenticate(Request $request)
    {
        // Get authentication details from request
        $email = null;
        $password = null;

        // Try Basic Auth header
        if ($request->header('Authorization')) {
            $header = $request->header('Authorization');
            if (strpos($header, 'Basic ') === 0) {
                $base64Credentials = substr($header, 6);
                $credentials = base64_decode($base64Credentials);
                if (strpos($credentials, ':') !== false) {
                    list($email, $password) = explode(':', $credentials, 2);
                }
            }
        }

        // Try PHP_AUTH_USER and PHP_AUTH_PW
        if (!$email && isset($_SERVER['PHP_AUTH_USER'])) {
            $email = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'] ?? '';
        }

        // Try JSON body
        if (!$email && $request->isJson()) {
            $jsonData = json_decode($request->getContent(), true) ?? [];
            $email = $jsonData['email'] ?? null;
            $password = $jsonData['password'] ?? null;
        }

        // Try request parameters
        if (!$email) {
            $email = $request->input('email');
            $password = $request->input('password');
        }

        // If still no credentials, return authentication error
        if (!$email || !$password) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'error' => 'Please provide valid login credentials'
            ], 401);
        }

        // Verify credentials
        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'error' => 'The provided credentials do not match our records'
            ], 401);
        }

        return $user;
    }

    /**
     * Get the authenticated user's profile.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProfile(Request $request)
    {
        // Try to use Sanctum auth first (Bearer token)
        $user = $request->user();
        
        // If token auth failed, try manual authentication
        if (!$user) {
            $user = $this->authenticate($request);
            if ($user instanceof JsonResponse) {
                return $user; // Return error response if authentication failed
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->user_id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'profile_picture' => $user->profile_picture,
                    'role' => $user->role
                ],
                'profile_complete' => !empty($user->first_name) && !empty($user->last_name) && !empty($user->phone)
            ]   
        ]);
    }

    /**
     * Validate token (helpful for testing)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateToken(Request $request)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'No token provided',
                'error' => 'Please provide a valid Bearer token in the Authorization header'
            ], 401);
        }
        
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
                'error' => 'The provided token is invalid or expired'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Token is valid',
            'data' => [
                'user' => [
                    'id' => $user->user_id,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ]
        ]);
    }

    /**
     * Process logout request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Properly revoke all tokens when using Sanctum
        if ($request->user()) {
            $request->user()->tokens()->delete();
        } else if ($request->bearerToken()) {
            // Try to find the token if user is not in the request
            $tokenId = explode('|', $request->bearerToken())[0] ?? null;
            if ($tokenId) {
                \Laravel\Sanctum\PersonalAccessToken::find($tokenId)?->delete();
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

}


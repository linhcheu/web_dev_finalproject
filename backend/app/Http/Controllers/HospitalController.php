<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class HospitalController extends Controller
{
    public function index(Request $request)
    {
        $query = Hospital::query();
        
        // Filter by ID
        if ($request->filled('hospital_id')) {
            $query->where('hospital_id', 'LIKE', '%' . $request->hospital_id . '%');
        }
        
        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }
        
        // Filter by province
        if ($request->filled('province') && $request->province !== 'all') {
            $query->where('province', $request->province);
        }
        
        // Filter by subscription status
        if ($request->filled('subscription_status') && $request->subscription_status !== 'all') {
            if ($request->subscription_status === 'has_subscription') {
                $query->whereHas('subscription');
            } else {
                $query->whereDoesntHave('subscription');
            }
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'hospital_id');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Validate sort fields
        $allowedSortFields = ['hospital_id', 'name', 'province', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'hospital_id';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }
        
        $query->orderBy($sortBy, $sortOrder);
        
        $totalHospitals = Hospital::count();
        $hospitals = $query->with('subscription')->paginate(10)->withQueryString();
        
        // Get unique provinces for filter dropdown
        $provinces = Hospital::distinct()->pluck('province')->filter()->values();

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'hospitals' => $hospitals->items(),
                    'pagination' => [
                        'current_page' => $hospitals->currentPage(),
                        'last_page' => $hospitals->lastPage(),
                        'per_page' => $hospitals->perPage(),
                        'total' => $hospitals->total()
                    ],
                    'filters' => [
                        'provinces' => $provinces
                    ],
                    'sort' => [
                        'sort_by' => $sortBy,
                        'sort_order' => $sortOrder
                    ],
                    'total_hospitals' => $totalHospitals
                ]
            ]);
        }
        
        return view('hospital', compact('totalHospitals', 'hospitals', 'provinces', 'sortBy', 'sortOrder'));
    }

    public function show(Request $request, $id)
    {
        $hospital = Hospital::with(['subscription', 'owner'])->findOrFail($id);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'hospital' => [
                        'id' => $hospital->hospital_id,
                        'name' => $hospital->name,
                        'province' => $hospital->province,
                        'location' => $hospital->location,
                        'contact_info' => $hospital->contact_info,
                        'information' => $hospital->information,
                        'profile_picture' => $hospital->profile_picture,
                        'status' => $hospital->status,
                        'created_at' => $hospital->created_at,
                        'updated_at' => $hospital->updated_at,
                        'owner' => $hospital->owner ? [
                            'id' => $hospital->owner->user_id,
                            'name' => $hospital->owner->first_name . ' ' . $hospital->owner->last_name,
                            'email' => $hospital->owner->email
                        ] : null,
                        'subscription' => $hospital->subscription ? [
                            'id' => $hospital->subscription->subscription_id,
                            'plan_type' => $hospital->subscription->plan_type,
                            'status' => $hospital->subscription->status,
                            'start_date' => $hospital->subscription->start_date,
                            'end_date' => $hospital->subscription->end_date
                        ] : null
                    ]
                ]
            ]);
        }

        return view('hospital_show', compact('hospital'));
    }

    public function edit(Request $request, $id)
    {
        $hospital = Hospital::with('subscription', 'owner')->findOrFail($id);
        
        // Get unique provinces for dropdown
        $provinces = Hospital::distinct()->pluck('province')->filter()->values();

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'hospital' => $hospital,
                    'provinces' => $provinces,
                    'form_fields' => [
                        'name' => [
                            'type' => 'text',
                            'required' => true,
                            'label' => 'Hospital Name'
                        ],
                        'province' => [
                            'type' => 'select',
                            'required' => true,
                            'label' => 'Province',
                            'options' => $provinces
                        ],
                        'location' => [
                            'type' => 'textarea',
                            'required' => true,
                            'label' => 'Location'
                        ],
                        'contact_info' => [
                            'type' => 'textarea',
                            'required' => true,
                            'label' => 'Contact Information'
                        ],
                        'information' => [
                            'type' => 'textarea',
                            'required' => false,
                            'label' => 'Additional Information'
                        ],
                        'status' => [
                            'type' => 'select',
                            'required' => true,
                            'label' => 'Status',
                            'options' => ['pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected']
                        ]
                    ]
                ]
            ]);
        }
        
        return view('hospital_edit', compact('hospital', 'provinces'));
    }

    public function update(Request $request, $id)
    {
        $hospital = Hospital::with('subscription', 'owner')->findOrFail($id);
        $updateType = $request->input('update_type');

        if ($updateType === 'all') {
            return $this->updateAll($request, $hospital);
        }

        switch ($updateType) {
            case 'administrator':
                return $this->updateAdministrator($request, $hospital);
            case 'profile_picture':
                return $this->updateProfilePicture($request, $hospital);
            case 'hospital_profile_picture':
                return $this->updateHospitalProfilePicture($request, $hospital);
            case 'basic_info':
                return $this->updateBasicInfo($request, $hospital);
            case 'contact_info':
                return $this->updateContactInfo($request, $hospital);
            default:
                return redirect()->route('hospital.edit', $hospital->hospital_id)->with('error', 'Invalid update type.');
        }
    }

    private function updateAll(Request $request, $hospital)
    {
        $user = $hospital->owner;

        $validated = $request->validate([
          
            // Hospital fields
            'name' => 'required|string|max:255',
            'province' => 'required|string|max:100',
            'location' => 'required|string|max:500',
            'contact_info' => 'required|string|max:500',
            'information' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user (admin/owner)
        if ($user) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password_hash = Hash::make($request->password);
            }
            $user->save();
        }

        // Update hospital
        $hospital->update([
            'name' => $request->name,
            'province' => $request->province,
            'location' => $request->location,
            'contact_info' => $request->contact_info,
            'information' => $request->information,
            'profile_picture' => $request->profile_picture,
        ]);

        return redirect()->route('hospital.edit', $hospital->hospital_id)->with('success', 'Hospital and administrator updated successfully!');
    }

    private function updateAdministrator(Request $request, $hospital)
    {
        $user = $hospital->owner;
        
        if (!$user) {
            return redirect()->route('hospital.edit', $hospital->hospital_id)->with('error', 'No administrator found for this hospital.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users_table,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|string|min:6|confirmed',
            'admin_profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user (admin/owner)
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password_hash = Hash::make($request->password);
        }

        // Handle admin profile picture upload
        if ($request->hasFile('admin_profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $userPath = $request->file('admin_profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $userPath;
        }

        $user->save();

        return redirect()->route('hospital.edit', $hospital->hospital_id)->with('success', 'Administrator information updated successfully!');
    }

    private function updateProfilePicture(Request $request, $hospital)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old profile picture if exists
        if ($hospital->profile_picture) {
            Storage::disk('public')->delete($hospital->profile_picture);
        }

        // Store new profile picture
        $profilePicturePath = $request->file('profile_picture')->store('hospital-profile-pictures', 'public');
        
        $hospital->update(['profile_picture' => $profilePicturePath]);

        return redirect()->route('hospital.edit', $hospital->hospital_id)->with('success', 'Hospital profile picture updated successfully!');
    }
    
    private function updateHospitalProfilePicture(Request $request, $hospital)
    {
        $request->validate([
            'hospital_profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old profile picture if exists
        if ($hospital->profile_picture) {
            Storage::disk('public')->delete($hospital->profile_picture);
        }

        // Store new profile picture
        $profilePicturePath = $request->file('hospital_profile_picture')->store('hospital-profile-pictures', 'public');
        
        $hospital->update(['profile_picture' => $profilePicturePath]);

        return redirect()->route('hospital.edit', $hospital->hospital_id)->with('success', 'Hospital profile picture updated successfully!');
    }

    private function updateBasicInfo(Request $request, $hospital)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'province' => 'required|string|max:100',
            'information' => 'nullable|string|max:1000',
            'location' => 'required|string|max:500',
            'hospital_profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
            'province' => $request->province,
            'information' => $request->information,
            'location' => $request->location,
        ];

        // Handle hospital profile picture upload
        if ($request->hasFile('hospital_profile_picture')) {
            if ($hospital->profile_picture) {
                Storage::disk('public')->delete($hospital->profile_picture);
            }
            $path = $request->file('hospital_profile_picture')->store('hospital-profile-pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        $hospital->update($updateData);

        return redirect()->route('hospital.edit', $hospital->hospital_id)->with('success', 'Hospital basic information updated successfully!');
    }

    private function updateContactInfo(Request $request, $hospital)
    {
        $request->validate([
            'contact_info' => 'required|string|max:500',
        ]);

        $hospital->update([
            'contact_info' => $request->contact_info,
        ]);

        return redirect()->route('hospital.edit', $hospital->hospital_id)->with('success', 'Hospital contact information updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $hospital = Hospital::findOrFail($id);
            $hospital->delete();
            
            return response()->json(['success' => true, 'message' => 'Hospital deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting hospital: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        // Get provinces for dropdown - this is a fallback in case the view needs it
        $provinces = [
            'Phnom Penh', 'Banteay Meanchey', 'Battambang', 'Kampong Cham',
            'Kampong Chhnang', 'Kampong Speu', 'Kampong Thom', 'Kampot',
            'Kandal', 'Kep', 'Koh Kong', 'Kratie', 'Mondulkiri',
            'Oddar Meanchey', 'Pailin', 'Preah Vihear', 'Prey Veng',
            'Pursat', 'Ratanakiri', 'Siem Reap', 'Sihanoukville',
            'Stung Treng', 'Svay Rieng', 'Takeo', 'Tbong Khmum'
        ];
        
        // Get available users who can be hospital admins
        $availableAdmins = User::where('role', 'hospital_admin')
            ->whereDoesntHave('hospitals')
            ->get();
        
        return view('hospital_create', compact('provinces', 'availableAdmins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'location' => 'required|string',
            'contact_info' => 'required|string',
            'information' => 'nullable|string',
            'owner_id' => 'nullable|exists:users_table,user_id',
            'status' => 'required|in:pending,active,rejected',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hospitalData = [
            'name' => $request->name,
            'province' => $request->province,
            'location' => $request->location,
            'contact_info' => $request->contact_info,
            'information' => $request->information,
            'owner_id' => $request->owner_id,
            'status' => $request->status,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('hospital-profile-pictures', 'public');
            $hospitalData['profile_picture'] = $profilePicturePath;
        }

        $hospital = Hospital::create($hospitalData);

        // Optionally create a subscription
        if ($request->has('create_subscription') && $request->create_subscription) {
            $hospital->subscriptions()->create([
                'plan_type' => $request->plan_type ?? 'basic',
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addYear(),
            ]);
        }

        return redirect()->route('hospital.index')
            ->with('success', 'Hospital created successfully!');
    }
}
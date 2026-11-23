<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HospitalController extends Controller
{
    private const IMAGE_RULE = 'image|mimes:jpeg,jpg,png,gif,webp|max:2048';

    private const PROVINCES = [
        'Banteay Meanchey', 'Battambang', 'Kampong Cham', 'Kampong Chhnang', 'Kampong Speu', 'Kampong Thom',
        'Kampot', 'Kandal', 'Kep', 'Koh Kong', 'Kratie', 'Mondulkiri', 'Oddar Meanchey', 'Pailin',
        'Phnom Penh', 'Preah Sihanouk', 'Preah Vihear', 'Prey Veng', 'Pursat', 'Ratanakiri', 'Siem Reap',
        'Stung Treng', 'Svay Rieng', 'Takeo', 'Tbong Khmum',
    ];

    public function index(Request $request): View
    {
        $hospitals = $this->api->paginate('admin/hospitals', Hospital::class, $request->only(
            'hospital_id', 'name', 'province', 'status', 'subscription_status', 'sort_by', 'sort_order', 'page'
        ));

        return view('hospital', [
            'hospitals' => $hospitals,
            'totalHospitals' => $hospitals->extra['total_all'],
            'provinces' => collect($hospitals->extra['provinces']),
            'sortBy' => $request->input('sort_by', 'hospital_id'),
            'sortOrder' => $request->input('sort_order', 'asc'),
        ]);
    }

    public function create(): View
    {
        $admins = $this->api->get('admin/users', ['role' => 'hospital_admin', 'without_hospital' => 1, 'per_page' => 100]);

        return view('hospital_create', [
            'provinces' => self::PROVINCES,
            'availableAdmins' => User::collectionFromApi($admins['data']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'new_province' => 'required_if:province,new|nullable|string|max:100',
            'location' => 'required|string|max:500',
            'contact_info' => 'required|string|max:500',
            'information' => 'nullable|string|max:2000',
            'owner_id' => 'nullable|integer',
            'status' => 'required|in:pending,active,rejected',
            'profile_picture' => 'nullable|'.self::IMAGE_RULE,
            'create_subscription' => 'nullable|boolean',
            'plan_type' => 'nullable|in:basic,premium,enterprise',
        ]);

        if ($data['province'] === 'new') {
            $data['province'] = $data['new_province'];
        }
        unset($data['new_province']);

        $this->api->post('admin/hospitals', array_filter($data, fn ($v) => $v !== null));

        return redirect()->route('hospital.index')->with('success', 'Hospital created successfully!');
    }

    public function edit(int $id): View
    {
        return view('hospital_edit', [
            'hospital' => Hospital::fromApi($this->api->get("admin/hospitals/$id")['data']),
            'provinces' => collect(self::PROVINCES),
        ]);
    }

    /** The edit page has one form per "update_type". */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = match ($request->input('update_type')) {
            'basic_info' => $request->validate([
                'name' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'location' => 'required|string|max:500',
                'information' => 'nullable|string|max:2000',
                'status' => 'sometimes|in:pending,active,rejected',
            ]),
            'contact_info' => $request->validate(['contact_info' => 'required|string|max:500']),
            'hospital_profile_picture' => ['profile_picture' => $request->validate([
                'hospital_profile_picture' => 'required|'.self::IMAGE_RULE,
            ])['hospital_profile_picture']],
            'status' => $request->validate(['status' => 'required|in:pending,active,rejected']),
            default => null,
        };

        if ($data === null) {
            return back()->with('error', 'Invalid update type.');
        }

        $this->api->post("admin/hospitals/$id", $data, ['profile_picture' => 'hospital_profile_picture']);

        return redirect()->route('hospital.edit', $id)->with('success', 'Hospital updated successfully!');
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['success' => true] + $this->api->delete("admin/hospitals/$id"));
    }
}

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
    public function index()
    {
        $totalHospitals = Hospital::count();
        $hospitals = Hospital::with('subscription')->latest()->paginate(10);

        return view('hospital', compact('totalHospitals', 'hospitals'));
    }

    public function edit($id)
    {
        $hospital = Hospital::with('subscription', 'owner')->findOrFail($id);
        return view('hospital_edit', compact('hospital'));
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
            // Admin fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users_table,email,' . ($user ? $user->user_id : 'NULL') . ',user_id',
            'password' => 'nullable|string|min:6|confirmed',
            // Hospital fields
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'contact_info' => 'required|string|max:500',
            'information' => 'nullable|string|max:1000',
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
            'location' => $request->location,
            'contact_info' => $request->contact_info,
            'information' => $request->information,
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
        $profilePicturePath = $request->file('profile_picture')->store('hospital-pictures', 'public');
        
        $hospital->update(['profile_picture' => $profilePicturePath]);

        return redirect()->route('hospital.edit', $hospital->hospital_id)->with('success', 'Hospital profile picture updated successfully!');
    }

    private function updateBasicInfo(Request $request, $hospital)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'information' => 'nullable|string|max:1000',
            'location' => 'required|string|max:500',
            'hospital_profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
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
} 
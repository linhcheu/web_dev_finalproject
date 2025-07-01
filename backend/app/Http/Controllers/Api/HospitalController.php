<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    public function index()
    {
        $hospitals = Hospital::active()
            ->withActiveSubscription()
            ->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $hospitals
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'required|string',
            'contact_info' => 'nullable|string',
            'information' => 'nullable|string',
            'image' => 'nullable|string',
            'owner_id' => 'nullable|exists:users_table,user_id',
        ]);

        $hospital = Hospital::create([
            'name' => $request->name,
            'location' => $request->location,
            'contact_info' => $request->contact_info,
            'information' => $request->information,
            'image' => $request->image,
            'owner_id' => $request->owner_id,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hospital created successfully',
            'data' => $hospital
        ], 201);
    }

    public function show(Hospital $hospital)
    {
        return response()->json([
            'success' => true,
            'data' => $hospital->load('appointments')
        ]);
    }

    public function update(Request $request, Hospital $hospital)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'location' => 'sometimes|required|string',
            'contact_info' => 'nullable|string',
            'information' => 'nullable|string',
            'image' => 'nullable|string',
            'owner_id' => 'nullable|exists:users_table,user_id',
            'status' => 'sometimes|required|in:pending,active,rejected'
        ]);

        $hospital->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Hospital updated successfully',
            'data' => $hospital
        ]);
    }

    public function destroy(Hospital $hospital)
    {
        $hospital->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hospital deleted successfully'
        ]);
    }
} 
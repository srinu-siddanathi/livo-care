<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string',
            'patient_mobile' => 'required|string',
            'test_ids' => 'required|array',
            'test_ids.*' => 'exists:tests,id'
        ]);

        $prescription = Prescription::create([
            'doctor_id' => $request->user()->id,
            'patient_name' => $request->patient_name,
            'patient_mobile' => $request->patient_mobile,
        ]);

        $prescription->tests()->attach($request->test_ids);

        return response()->json([
            'message' => 'Prescription created successfully',
            'prescription' => $prescription->load('tests')
        ]);
    }

    public function doctorPrescriptions(Request $request)
    {
        $prescriptions = $request->user()->prescriptions()
            ->with('tests')
            ->latest()
            ->get();

        return response()->json($prescriptions);
    }

    public function allPrescriptions()
    {
        $prescriptions = Prescription::with(['doctor', 'tests'])
            ->latest()
            ->get();

        return response()->json($prescriptions);
    }

    public function updateStatus(Request $request, Prescription $prescription)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $prescription->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status updated successfully',
            'prescription' => $prescription->load(['doctor', 'tests'])
        ]);
    }
} 
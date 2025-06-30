<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DoctorAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_id' => 'nullable|string'
        ]);

        $doctor = Doctor::where('email', $request->email)->first();

        if (!$doctor || !Hash::check($request->password, $doctor->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Store device token if provided
        if ($request->device_id) {
            DeviceToken::updateOrCreate(
                [
                    'tokenable_type' => Doctor::class,
                    'tokenable_id' => $doctor->id,
                    'device_id' => $request->device_id
                ],
                [
                    'device_id' => $request->device_id
                ]
            );
        }

        $token = $doctor->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'doctor' => $doctor
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:doctors',
            'password' => 'required|string|min:8',
            'specialization' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $doctor = Doctor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'specialization' => $request->specialization,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
        ]);

        $token = $doctor->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Doctor registered successfully',
            'data' => [
                'doctor' => $doctor,
                'token' => $token,
            ]
        ], 201);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:doctors,email,' . auth()->id(),
            'specialization' => 'sometimes|string|max:255',
            'mobile' => 'sometimes|string|max:20',
            'gender' => 'sometimes|in:male,female,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $doctor = auth()->user();
        
        // Only update fields that are present in the request
        if ($request->has('name')) {
            $doctor->name = $request->name;
        }
        if ($request->has('email')) {
            $doctor->email = $request->email;
        }
        if ($request->has('specialization')) {
            $doctor->specialization = $request->specialization;
        }
        if ($request->has('mobile')) {
            $doctor->mobile = $request->mobile;
        }
        if ($request->has('gender')) {
            $doctor->gender = $request->gender;
        }

        $doctor->save();

        return response()->json([
            'message' => 'Doctor information updated successfully',
            'data' => $doctor
        ]);
    }

    public function index()
    {
        // Get doctors with prescription counts
        $doctors = Doctor::select([
            'doctors.id',
            'doctors.name',
            'doctors.email',
            'doctors.mobile',
            'doctors.gender',
            'doctors.specialization',
            'doctors.created_at'
        ])
        ->selectRaw('(SELECT COUNT(*) FROM prescriptions WHERE prescriptions.doctor_id = doctors.id) as prescriptions_count')
        ->get();

        \Log::info('Doctors query result:', [
            'raw_data' => $doctors->toArray()
        ]);

        $mappedDoctors = $doctors->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'email' => $doctor->email,
                'mobile' => $doctor->mobile,
                'gender' => $doctor->gender,
                'specialization' => $doctor->specialization,
                'prescriptions_count' => (int) $doctor->prescriptions_count,
                'created_at' => $doctor->created_at->format('Y-m-d')
            ];
        });

        return response()->json([
            'message' => 'Doctors retrieved successfully',
            'data' => $mappedDoctors
        ]);
    }
} 
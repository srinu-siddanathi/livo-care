<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticCenter;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DiagnosticAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_id' => 'nullable|string'
        ]);

        $center = DiagnosticCenter::where('email', $request->email)->first();

        if (!$center || !Hash::check($request->password, $center->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Store device token if provided
        if ($request->device_id) {
            DeviceToken::updateOrCreate(
                [
                    'tokenable_type' => DiagnosticCenter::class,
                    'tokenable_id' => $center->id,
                    'device_id' => $request->device_id
                ],
                [
                    'device_id' => $request->device_id
                ]
            );
        }

        $token = $center->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'center' => $center
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
} 
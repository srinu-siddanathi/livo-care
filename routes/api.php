<?php

use App\Http\Controllers\Auth\DiagnosticAuthController;
use App\Http\Controllers\Auth\DoctorAuthController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

// Test route to verify API is working
Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

// Authentication Routes
Route::post('/doctor/login', [DoctorAuthController::class, 'login']);
Route::post('/diagnostic/login', [DiagnosticAuthController::class, 'login']);

// Add this new route for fetching tests
Route::get('/tests', [TestController::class, 'index']);

// Doctor Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/doctor/logout', [DoctorAuthController::class, 'logout']);
    Route::post('/prescriptions', [PrescriptionController::class, 'store']);
    Route::get('/doctor/prescriptions', [PrescriptionController::class, 'doctorPrescriptions']);
});

// Diagnostic Center Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/diagnostic/logout', [DiagnosticAuthController::class, 'logout']);
    Route::get('/prescriptions', [PrescriptionController::class, 'allPrescriptions']);
    Route::patch('/prescriptions/{prescription}/status', [PrescriptionController::class, 'updateStatus']);
}); 
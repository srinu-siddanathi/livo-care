<?php

use App\Http\Controllers\Auth\DiagnosticAuthController;
use App\Http\Controllers\Auth\DoctorAuthController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Test route to verify API is working
Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

// Authentication Routes
Route::post('/doctor/register', [DoctorAuthController::class, 'register']);
Route::post('/doctor/login', [DoctorAuthController::class, 'login']);
Route::post('/diagnostic/login', [DiagnosticAuthController::class, 'login']);

// Add this new route for fetching tests
Route::get('/tests', [TestController::class, 'index']);

// Doctor Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/doctor/logout', [DoctorAuthController::class, 'logout']);
    Route::put('/doctor/profile', [DoctorAuthController::class, 'update']);
    Route::post('/prescriptions', [PrescriptionController::class, 'store']);
    Route::get('/doctor/prescriptions', [PrescriptionController::class, 'doctorPrescriptions']);
});

// Diagnostic Center Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/diagnostic/logout', [DiagnosticAuthController::class, 'logout']);
    Route::get('/doctors', [DoctorAuthController::class, 'index']);
    
    // Move the specific route before the general one
    Route::get('/prescriptions/by-doctor/{doctor_id}', [PrescriptionController::class, 'prescriptionsByDoctor']);
    Route::get('/prescriptions', [PrescriptionController::class, 'allPrescriptions']);
    Route::patch('/prescriptions/{prescription}/status', [PrescriptionController::class, 'updateStatus']);
});

// Test Routes (for development only)
Route::post('/test/notification', function(Request $request) {
    $deviceToken = 'ePtKUGJHRHqxZJf50w8rr_:APA91bEqz3KUsvzHw0lBo4GcpKxWpcuJg2aLUhXagArvgWo_v6nKFQMSkUb37dcbVSz7Kxst8J1wZFobrxpBIMaGysVER-_OdUNqs0KT1U9PO0BPSxCSbFc';

    $fcmService = new \App\Services\FCMService();
    $result = $fcmService->sendNotification(
        $deviceToken,
        'Test Notification',
        $request->input('message', 'This is a test notification'),
        [
            'test' => true,
            'timestamp' => now()->toISOString()
        ]
    );

    return response()->json([
        'message' => $result['success'] ? 'Notification sent' : 'Notification failed',
        'result' => $result
    ]);
}); 
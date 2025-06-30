<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\DeviceToken;
use App\Models\DiagnosticCenter;
use App\Services\FCMService;
use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Validator;

class PrescriptionController extends Controller
{
    private $fcmService;

    public function __construct()
    {
        $this->fcmService = new FCMService();
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string',
            'patient_mobile' => 'required|string',
            'test_ids' => 'required|string',
            'patient_age' => 'nullable|integer',
            'patient_gender' => 'nullable|in:male,female,other',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:4096'
        ]);

        // Split the comma-separated string into an array
        $testIds = array_filter(explode(',', $request->test_ids));
        
        // Additional validation for test IDs if needed
        Validator::make(['test_ids' => $testIds], [
            'test_ids' => 'array',
            'test_ids.*' => 'exists:tests,id'
        ])->validate();

        try {
            // Handle image upload
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                
                // Store directly in the public disk under prescriptions directory
                $path = $request->file('image')->storeAs('prescriptions', $filename, 'public');
                $imageUrl = "storage/$path"; // This will be 'prescriptions/filename.jpg'
            }

            $prescription = Prescription::create([
                'doctor_id' => auth()->id(),
                'patient_name' => $request->patient_name,
                'patient_mobile' => $request->patient_mobile,
                'patient_age' => $request->patient_age,
                'patient_gender' => $request->patient_gender,
                'notes' => $request->notes,
                'status' => 'pending',
                'image_url' => $imageUrl
            ]);

            $prescription->tests()->attach($testIds);

            // Load the doctor relationship before sending notification
            $prescription->load(['doctor:id,name,email']);

            // Debug log before sending notification
            \Log::info('New prescription created:', [
                'prescription_id' => $prescription->id,
                'doctor_id' => auth()->id(),
                'doctor_name' => $prescription->doctor->name ?? 'Not loaded',
                'patient_name' => $prescription->patient_name,
                'image_url' => $imageUrl
            ]);

            // Send notification in background
            $this->sendPrescriptionNotification($prescription);

            return response()->json([
                'message' => 'Prescription created successfully',
                'data' => $prescription->load('tests')
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error creating prescription:', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => 'Error creating prescription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function doctorPrescriptions(Request $request)
    {
        $prescriptions = $request->user()->prescriptions()
            ->with('tests')
            ->latest()
            ->get();

        return response()->json($prescriptions);
    }

    public function allPrescriptions(Request $request)
    {
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        if(!$limit){
            $limit = 10;
        }
        if(!$offset){
            $offset = 0;
        }
        
        $prescriptions = Prescription::with(['doctor', 'tests'])
            ->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $total = Prescription::count();
        
        return response()->json([
            'message' => 'Prescriptions retrieved successfully',
            'data' => [
                'prescriptions' => $prescriptions,
                'total' => $total,
                'offset' => $offset,
                'limit' => $limit
            ]
        ]);
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

    private function sendPrescriptionNotification($prescription)
    {
        try {
            // Make sure doctor relationship is loaded
            if (!$prescription->relationLoaded('doctor')) {
                $prescription->load('doctor');
            }

            // Debug log to check doctor and patient details
            \Log::info('Prescription details for notification:', [
                'prescription_id' => $prescription->id,
                'doctor_name' => $prescription->doctor->name ?? 'Not loaded',
                'patient_name' => $prescription->patient_name
            ]);

            $deviceTokens = DeviceToken::where('tokenable_type', DiagnosticCenter::class)
                ->pluck('device_id')
                ->toArray();

            $notificationResults = [
                'device_tokens_found' => count($deviceTokens),
                'notifications_sent' => [],
                'errors' => []
            ];

            if (!empty($deviceTokens)) {
                foreach ($deviceTokens as $token) {
                    try {
                        // Create notification message with verified data
                        $notificationMessage = sprintf(
                            "Dr. %s has created a new prescription for %s",
                            $prescription->doctor->name ?? 'Unknown Doctor',
                            $prescription->patient_name ?? 'Unknown Patient'
                        );

                        $result = $this->fcmService->sendNotification(
                            $token,
                            'New Prescription Available',
                            $notificationMessage,
                            [
                                'prescription_id' => $prescription->id,
                                'doctor_name' => $prescription->doctor->name ?? 'Unknown Doctor',
                                'patient_name' => $prescription->patient_name,
                                'patient_mobile' => $prescription->patient_mobile,
                                'created_at' => $prescription->created_at->toISOString(),
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'screen' => 'prescriptions'
                            ]
                        );

                        $notificationResults['notifications_sent'][] = [
                            'token' => $token,
                            'result' => $result,
                            'message' => $notificationMessage // Log the actual message sent
                        ];

                        \Log::info('Prescription notification sent:', [
                            'token' => $token,
                            'prescription_id' => $prescription->id,
                            'message' => $notificationMessage,
                            'result' => $result
                        ]);
                    } catch (\Exception $e) {
                        $notificationResults['errors'][] = [
                            'token' => $token,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            }

            return $notificationResults;

        } catch (\Exception $e) {
            \Log::error('Error sending prescription notification:', [
                'error' => $e->getMessage(),
                'prescription_id' => $prescription->id,
                'doctor_id' => $prescription->doctor_id ?? 'Not available',
                'patient_name' => $prescription->patient_name ?? 'Not available'
            ]);

            return [
                'device_tokens_found' => 0,
                'notifications_sent' => [],
                'errors' => [$e->getMessage()],
                'error_type' => get_class($e)
            ];
        }
    }

    /**
     * Get prescriptions for a specific doctor
     */
    public function prescriptionsByDoctor($doctor_id, Request $request)
    {
        // Validate doctor exists
        $doctor = Doctor::findOrFail($doctor_id);

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        
        // Get doctor with their prescriptions
        $doctor = Doctor::with(['prescriptions' => function ($query) use ($limit, $offset) {
            $query->with('tests')
                  ->orderBy('created_at', 'desc')
                  ->skip($offset)
                  ->take($limit);
        }])
        ->select([
            'id',
            'name',
            'email',
            'mobile',
            'specialization',
            'gender'
        ])
        ->withCount('prescriptions')
        ->findOrFail($doctor_id);

        // Format the response
        $data = [
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'email' => $doctor->email,
                'mobile' => $doctor->mobile,
                'specialization' => $doctor->specialization,
                'gender' => $doctor->gender,
                'prescriptions_count' => $doctor->prescriptions_count
            ],
            'prescriptions' => $doctor->prescriptions->map(function ($prescription) {
                return [
                    'id' => $prescription->id,
                    'patient_name' => $prescription->patient_name,
                    'patient_mobile' => $prescription->patient_mobile,
                    'patient_age' => $prescription->patient_age,
                    'patient_gender' => $prescription->patient_gender,
                    'status' => $prescription->status,
                    'notes' => $prescription->notes,
                    'image_url' => $prescription->image_url ? url($prescription->image_url) : null,
                    'created_at' => $prescription->created_at->format('Y-m-d H:i:s'),
                    'tests' => $prescription->tests->map(function ($test) {
                        return [
                            'id' => $test->id,
                            'name' => $test->name,
                            'price' => $test->price
                        ];
                    })
                ];
            })
        ];

        return response()->json([
            'message' => 'Doctor prescriptions retrieved successfully',
            'data' => $data,
            'meta' => [
                'total' => $doctor->prescriptions_count,
                'offset' => $offset,
                'limit' => $limit
            ]
        ]);
    }
} 
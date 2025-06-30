<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FCMService
{
    private $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = storage_path('app/json/livocare-cb897.json');
            
            // Debug line
            \Log::info('Firebase credentials path:', [
                'path' => $credentialsPath,
                'exists' => file_exists($credentialsPath),
                'readable' => is_readable($credentialsPath),
                'contents' => file_exists($credentialsPath) ? json_decode(file_get_contents($credentialsPath)) : null
            ]);
            
            // Check if credentials file exists and is readable
            if (!file_exists($credentialsPath) || !is_readable($credentialsPath)) {
                Log::error('Firebase credentials file not found or not readable', [
                    'path' => $credentialsPath
                ]);
                throw new \Exception('Firebase credentials not accessible');
            }

            $factory = (new Factory)
                ->withServiceAccount($credentialsPath);
            
            $this->messaging = $factory->createMessaging();

        } catch (\Exception $e) {
            Log::error('Firebase init error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function sendNotification($deviceTokens, $title, $body, $data = [])
    {
        try {
            if (!$this->messaging) {
                throw new \Exception('Firebase messaging not initialized');
            }

            $token = is_array($deviceTokens) ? $deviceTokens[0] : $deviceTokens;

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData(array_merge($data, [
                    'timestamp' => time()
                ]));

            $response = $this->messaging->send($message);

            Log::info('FCM Response:', ['response' => $response]);

            return [
                'success' => true,
                'response' => $response,
                'device_tokens' => $deviceTokens
            ];

        } catch (\Exception $e) {
            Log::error('FCM error:', [
                'error' => $e->getMessage(),
                'token' => $deviceTokens
            ]);
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
} 
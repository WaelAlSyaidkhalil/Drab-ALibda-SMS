<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $credentials = config('services.firebase.credentials');

        if (!$credentials) {
            throw new \RuntimeException('FIREBASE_CREDENTIALS is not configured.');
        }

        $credentialsPath = Str::startsWith($credentials, '/')
            ? $credentials
            : base_path($credentials);

        if (!file_exists($credentialsPath)) {
            throw new \RuntimeException(
                "Firebase credentials file not found: {$credentialsPath}"
            );
        }

        $factory = (new Factory)
            ->withServiceAccount($credentialsPath);

        $this->messaging = $factory->createMessaging();
    }

    public function sendPushNotification($tokens, $title, $body)
    {
        foreach ($tokens as $token) {
            try {
                $message = [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ];

                $this->messaging->send($message);
            } catch (\Exception $e) {
                Log::error(
                    'Firebase Notification Error: ' . $e->getMessage()
                );

                return false;
            }
        }

        return true;
    }
}

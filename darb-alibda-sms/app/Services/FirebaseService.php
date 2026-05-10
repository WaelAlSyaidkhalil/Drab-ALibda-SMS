<?php


namespace App\Services;

use Kreait\Firebase\Factory;
use App\Models\User;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        // تهيئة المصنع مرة واحدة عند استدعاء الخدمة
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials.file'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendPushNotification(User $user, $title, $body)
    {
        if (!$user->fcm_token) {
            return false;
        }

        $message = [
            'token' => $user->fcm_token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        try {

            $this->messaging->send($message);
            return true;
        }
        catch (\Exception $e) {

            \Log::error("Firebase Notification Error: " . $e->getMessage());
            return false;
        }
    }
}



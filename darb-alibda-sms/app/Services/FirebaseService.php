<?php


namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use App\Models\Auth\User;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        // تهيئة المصنع مرة واحدة عند استدعاء الخدمة
        $credentials = config('firebase.projects.'.config('firebase.default').'.credentials');

        if ($credentials) {
            $factory = (new Factory)->withServiceAccount($credentials);
        } else {
            $factory = new Factory();
        }

        $this->messaging = $factory->createMessaging();
    }

    public function sendPushNotification($tokens, $title, $body)
    {
        foreach($tokens as $token)
        {

            $message = [
                'token' => $token,
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

                Log::error("Firebase Notification Error: " . $e->getMessage());
                return false;
            }
        }
    }
}



<?php

namespace App\Jobs;

use App\Services\FirebaseService;
use Illuminate\Bus\Dispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendFirebaseNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $tokens,
        public string $title,
        public string $body
    ) {
    }

    public function handle(FirebaseService $firebaseService): void
    {
        $firebaseService->sendPushNotification($this->tokens, $this->title, $this->body);
    }
}

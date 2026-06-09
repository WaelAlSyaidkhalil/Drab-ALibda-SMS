<?php

namespace App\Listeners\Teacher;

use App\Events\Teacher\TeacherLoginFailed;
use Illuminate\Support\Facades\Log;

class LogFailedLoginAttempt
{
    public function handle(TeacherLoginFailed $event): void
    {
        Log::warning('Teacher login failed.', [
            'phone' => $event->phone,
            'ip' => $event->ip,
            'reason' => $event->reason,
        ]);
    }
}

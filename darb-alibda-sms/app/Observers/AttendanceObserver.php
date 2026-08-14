<?php

namespace App\Observers;

use App\Models\Schedule\Attendance;
use App\Notifications\StudentAttendanceChanged;
use App\Enums\AttendanceStatus;

class AttendanceObserver
{
    private function shouldNotify(Attendance $attendance): bool
    {
        $status = is_object($attendance->status) ? $attendance->status->value : $attendance->status;
        return in_array($status, [AttendanceStatus::ABSENT->value, AttendanceStatus::LATE->value], true);
    }

    public function created(Attendance $attendance): void
    {
        if (!$this->shouldNotify($attendance)) {
            return;
        }

        $attendance->loadMissing('student.parent', 'student.user');

        $notification = new StudentAttendanceChanged($attendance, 'تم التسجيل');

        // Notify parent
        if ($attendance->student?->parent instanceof \App\Models\Auth\User) {
            $attendance->student->parent->notify($notification);
        }

        // Notify student user account if exists
        if ($attendance->student?->user instanceof \App\Models\Auth\User) {
            $attendance->student->user->notify($notification);
        }
    }

    public function updated(Attendance $attendance): void
    {
        if (!$attendance->wasChanged('status')) {
            return;
        }

        if (!$this->shouldNotify($attendance)) {
            return;
        }

        $attendance->loadMissing('student.parent', 'student.user');

        $notification = new StudentAttendanceChanged($attendance, 'تم التحديث');

        if ($attendance->student?->parent instanceof \App\Models\Auth\User) {
            $attendance->student->parent->notify($notification);
        }

        if ($attendance->student?->user instanceof \App\Models\Auth\User) {
            $attendance->student->user->notify($notification);
        }
    }
}

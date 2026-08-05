<?php

namespace App\Jobs;

use App\Enums\AttendanceStatus;
use App\Models\Academic\Teacher;
use App\Models\Schedule\TeacherAttendance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FillTeacherAttendanceTableJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach (Teacher::active()->get() as $teacher) {
            TeacherAttendance::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'date' => today(),
                ],
                [
                    'status' => AttendanceStatus::PRESENT,
                ]
            );
        }
    }
}

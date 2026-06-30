<?php

namespace App\Jobs;

use App\Enums\AttendanceStatus;
use App\Models\Academic\Section;
use App\Models\Schedule\Attendance;
use App\Models\Schedule\Schedule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FillAttendanceTableJob implements ShouldQueue
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
        foreach(Section::all() as $section)
        {
            $enrollments = $section->enrollments()->active()->get();

            foreach($enrollments as $enrollment)
            {
                Attendance::create([
                    'student_id' => $enrollment->student_id,
                    'section_id' => $section->id,
                    'date' => today(),
                    'status' => AttendanceStatus::PRESENT
                ]);
            }
        }
    }
}

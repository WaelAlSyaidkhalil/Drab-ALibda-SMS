<?php

namespace App\Filament\Resources\TeacherAttendances\Pages;

use App\Filament\Resources\TeacherAttendances\TeacherAttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListTeacherAttendances extends ListRecords
{
    protected static string $resource = TeacherAttendanceResource::class;

    public function getTitle(): string
    {
        $date = request('date');
        $teacherId = request('teacher_id');

        $teacher = $teacherId
            ? \App\Models\Academic\Teacher::find($teacherId)?->full_name
            : null;

        $parts = [];

        if ($date) {
            $parts[] = $date;
        }

        if ($teacher) {
            $parts[] = $teacher;
        }

        return $parts
            ? implode('  |  ', $parts)
            : __('dashboard.pages.teacher_attendance');
    }
}

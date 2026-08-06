<?php

namespace App\Filament\Pages\Scheduling\Traits;

use App\Models\Academic\Section;
use App\Models\Academic\Teacher;
use App\Models\Auth\User;
use App\Models\Schedule\Schedule;
use App\Notifications\Admin\TimetablePublishedNotification;
use App\Services\FirebaseService;

trait PublishesClassTimetableNotifications
{
    protected function publishClassTimetable(int $termId, array $classIds): array
    {
        if (empty($classIds)) {
            return [
                'success' => false,
                'reason' => 'no_class_ids',
            ];
        }

        $scheduleExists = Schedule::query()
            ->where('term_id', $termId)
            ->whereHas('section', fn ($query) => $query->whereIn('class_id', $classIds))
            ->exists();

        if (! $scheduleExists) {
            return [
                'success' => false,
                'reason' => 'no_schedule',
            ];
        }

        $students = User::students()
            ->whereHas('student.enrollments', fn ($query) => $query
                ->where('status', 'active')
                ->whereHas('section', fn ($query) => $query->whereIn('class_id', $classIds))
            )
            ->get();

        $teachers = Teacher::query()
            ->whereHas('subjects', fn ($query) => $query->whereIn('class_id', $classIds))
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->with('user')
            ->get();

        if ($students->isEmpty() && $teachers->isEmpty()) {
            return [
                'success' => false,
                'reason' => 'no_recipients',
            ];
        }

        $studentNotification = new TimetablePublishedNotification('students');
        $teacherNotification = new TimetablePublishedNotification('teachers');

        foreach ($students as $student) {
            $student->notifyNow($studentNotification);
        }

        foreach ($teachers as $teacher) {
            if ($teacher->user) {
                $teacher->user->notifyNow($teacherNotification);
            }
        }

        $studentTokens = $students
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->unique()
            ->values()
            ->toArray();

        $teacherTokens = $teachers
            ->pluck('user.fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (! empty($studentTokens)) {
            app(FirebaseService::class)
                ->sendPushNotification($studentTokens, $studentNotification->title(), $studentNotification->body());
        }

        if (! empty($teacherTokens)) {
            app(FirebaseService::class)
                ->sendPushNotification($teacherTokens, $teacherNotification->title(), $teacherNotification->body());
        }

        return [
            'success' => true,
            'students' => $students->count(),
            'teachers' => $teachers->count(),
            'student_tokens' => count($studentTokens),
            'teacher_tokens' => count($teacherTokens),
        ];
    }

    protected function getClassIdFromSection(int $sectionId): ?int
    {
        return Section::find($sectionId)?->class_id;
    }

    protected function getClassIdsForTeacher(int $termId, int $teacherId): array
    {
        return Schedule::query()
            ->where('term_id', $termId)
            ->whereHas('subject', fn ($query) => $query->where('teacher_id', $teacherId))
            ->with('section')
            ->get()
            ->pluck('section.class_id')
            ->unique()
            ->filter()
            ->values()
            ->toArray();
    }
}

<?php

namespace App\Repositories\Teacher;

use App\Models\Academic\StudentEnrollment;
use App\Models\Grading\StudentMark;
use App\Models\Schedule\Schedule;
use App\Models\Subjects\SubjectComponent;
use Illuminate\Database\Eloquent\Builder;

class TeacherMarkRepository
{
    public function findActiveEnrollment(int $enrollmentId): ?StudentEnrollment
    {
        return StudentEnrollment::query()
            ->active()
            ->with(['section', 'student.user'])
            ->find($enrollmentId);
    }

    public function getTeacherStudentsForGrading(int $teacherId, ?int $classId = null, ?int $sectionId = null, ?int $subjectId = null, ?int $termId = null): array
    {
        return []; // لم يعد مستخدم الآن
    }

    /**
     * Return sections that the teacher teaches with active students and
     * only grades for the subjects the teacher teaches in each section.
     *
     * @return array
     */
    public function getTeacherSectionsWithStudentsAndGrades(
        int $teacherId,
        ?int $classId = null,
        ?int $sectionId = null,
        ?int $subjectId = null,
        ?int $termId = null
    ): array {
        $query = Schedule::query()
            ->where('teacher_id', $teacherId)
            ->when($classId, fn ($q) => $q->whereHas('section', fn ($sq) => $sq->where('class_id', $classId)))
            ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
            ->when($subjectId, fn ($q) => $q->where('subject_id', $subjectId))
            ->when($termId, fn ($q) => $q->where('term_id', $termId))
            ->with([
                'section.schoolClass',
                'section.enrollments' => fn ($q) => $q->active()->with(['student.user']),
                'subject',
                'term'
            ]);

        $schedules = $query->get();

        $sections = [];

        // group schedules by section
        $schedulesBySection = $schedules->groupBy('section_id');

        foreach ($schedulesBySection as $sectionIdKey => $sectionSchedules) {
            $section = $sectionSchedules->first()->section;
            $class = $section->schoolClass;

            // subjects this teacher teaches in this section
            $subjectIds = $sectionSchedules->pluck('subject_id')->unique()->values()->all();

            // build students list with grades limited to subjectIds
            $students = [];

            foreach ($section->enrollments as $enrollment) {
                $student = $enrollment->student;

                // fetch marks for this enrollment limited to subjects taught by teacher
                $marksQuery = StudentMark::query()
                    ->where('enrollment_id', $enrollment->id)
                    ->whereIn('subject_id', $subjectIds)
                    ->with(['subject', 'subjectComponent']);

                if ($termId) {
                    $marksQuery->where('term_id', $termId);
                }

                $marks = $marksQuery->get();

                // ✔️ إضافة id العلامة هنا
                $grades = $marks->map(fn ($m) => [
                    'id' => $m->id, // ← رقم العلامة
                    'subject_id' => $m->subject_id,
                    'subject_name' => $m->subject?->name,
                    'type' => $m->subjectComponent?->type,
                    'score' => $m->mark,
                    'component_id' => $m->subject_component_id,
                ])->values()->all();

                $students[] = [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'enrollment_id' => $enrollment->id,
                    'status' => $enrollment->status,
                    'grades' => $grades,
                ];
            }

            $sections[$sectionIdKey] = [
                'section_id' => $section->id,
                'section_name' => $section->name,
                'class' => $class?->name,
                'students' => $students,
            ];
        }

        return array_values($sections);
    }
public function resolveEnrollmentForTeacher(
    int $teacherId,
    ?int $enrollmentId = null,
    ?int $studentId = null,
    ?int $classId = null,
    ?int $sectionId = null
): ?StudentEnrollment {
    if ($enrollmentId) {
        return $this->findActiveEnrollment($enrollmentId);
    }

    if ($studentId) {
        $query = StudentEnrollment::query()
            ->active()
            ->with(['section', 'student.user'])
            ->where('student_id', $studentId);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        } elseif ($classId) {
            $query->whereHas('section', function ($sectionQuery) use ($classId): void {
                $sectionQuery->where('class_id', $classId);
            });
        }

        return $query->first();
    }

    return null;
}

    public function teacherTeaches(int $teacherId, int $sectionId, int $subjectId, int $termId): bool
    {
        return Schedule::query()
            ->where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->exists();
    }

    public function findSubjectComponent(int $componentId): ?SubjectComponent
    {
        return SubjectComponent::find($componentId);
    }

    public function findTeacherMark(int $markId, int $teacherId): ?StudentMark
    {
        return StudentMark::query()
            ->where('id', $markId)
            ->whereHas('enrollment.section.schedules', fn ($q) =>
                $q->where('teacher_id', $teacherId)
            )
            ->with(['enrollment.section', 'enrollment.student.user', 'subject', 'subjectComponent', 'term'])
            ->first();
    }
}

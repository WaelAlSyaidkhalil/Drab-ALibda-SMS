<?php

namespace App\Services\Teacher;

use App\Models\Academic\Student;
use App\Models\Academic\Teacher;
use App\Models\Auth\User;
use App\Models\Grading\StudentMark;
use App\Models\Subjects\SubjectComponent;
use App\Notifications\Parent\TeacherActionNotification;
use App\Repositories\Teacher\TeacherMarkRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeacherMarkService
{
    public function __construct(protected TeacherMarkRepository $repository)
    {
    }

    /**
     * ❌ تم حذف getTeacherMarks() بالكامل لأنها لم تعد مستخدمة.
     */

    public function getTeacherStudentsForGrading(int $teacherId, ?int $classId = null, ?int $sectionId = null, ?int $subjectId = null, ?int $termId = null): array
    {
        return $this->repository->getTeacherStudentsForGrading($teacherId, $classId, $sectionId, $subjectId, $termId);
    }

    public function getTeacherSectionsWithStudentsAndGrades(int $teacherId, ?int $classId = null, ?int $sectionId = null, ?int $subjectId = null, ?int $termId = null): array
    {
        return $this->repository->getTeacherSectionsWithStudentsAndGrades($teacherId, $classId, $sectionId, $subjectId, $termId);
    }

    public function createStudentMark(
        int $teacherId,
        ?int $enrollmentId = null,
        ?int $studentId = null,
        ?int $classId = null,
        ?int $sectionId = null,
        int $subjectId,
        int $subjectComponentId,
        int $termId,
        float $mark
    ): array {
        $enrollment = $this->repository->resolveEnrollmentForTeacher($teacherId, $enrollmentId, $studentId, $classId, $sectionId);

        if (!$enrollment) {
            throw new ModelNotFoundException('لم يتم العثور على تسجيل الطالب المطلوب في الصف أو الشعبة المحددة أو التسجيل غير نشط.');
        }

        $subjectComponent = $this->repository->findSubjectComponent($subjectComponentId);

        if (!$subjectComponent) {
            throw new ModelNotFoundException('مكون المادة غير موجود.');
        }

        if ($subjectComponent->subject_id !== $subjectId) {
            throw new \Exception('المكون المحدد لا ينتمي إلى هذه المادة.');
        }

        if (! $this->repository->teacherTeaches($teacherId, $enrollment->section_id, $subjectId, $termId)) {
            throw new \Exception('لا تمتلك صلاحية إضافة علامة لهذا الطالب في هذه المادة أو الفصل.');
        }

        $this->validateMarkValue($mark, $subjectComponent);
        $this->validateUniqueMark($enrollment->id, $subjectId, $subjectComponentId, $termId);

        $studentMark = StudentMark::create([
            'enrollment_id' => $enrollment->id,
            'subject_id' => $subjectId,
            'subject_component_id' => $subjectComponentId,
            'term_id' => $termId,
            'mark' => $mark,
        ]);

        $studentMark->load(['enrollment.section', 'enrollment.student.user', 'subject', 'subjectComponent', 'term']);

        $student = $studentMark->enrollment?->student;
        $teacherUser = Teacher::find($teacherId)?->user;
        if ($student && $student->parent && $teacherUser) {
            $student->parent->notifyNow(new TeacherActionNotification(
                $teacherUser,
                $student,
                'تم إضافة علامة جديدة',
                sprintf('تم إضافة علامة جديدة ل %s في المادة %s.', $student->getFullNameAttribute(), $studentMark->subject->name),
                ['type' => 'mark']
            ));
        }

        return $this->formatMark($studentMark);
    }

    /**
     * ✔️ الدالة الجديدة: تعديل العلامة باستخدام markId فقط
     */
    public function updateStudentMarkSimple(int $teacherId, int $markId, float $mark): array
    {
        $studentMark = $this->repository->findTeacherMark($markId, $teacherId);

        if (!$studentMark) {
            throw new ModelNotFoundException('العلامة غير موجودة أو لا تملك صلاحية الوصول إليها.');
        }

        $subjectComponent = $studentMark->subjectComponent;

        // التحقق من قيمة العلامة
        $this->validateMarkValue($mark, $subjectComponent);

        // تحديث العلامة فقط
        $studentMark->update([
            'mark' => $mark,
        ]);

        $studentMark->refresh();
        $studentMark->load(['enrollment.section', 'enrollment.student.user', 'subject', 'subjectComponent', 'term']);

        $student = $studentMark->enrollment?->student;
        $teacherUser = Teacher::find($teacherId)?->user;
        if ($student && $student->parent && $teacherUser) {
            $student->parent->notifyNow(new TeacherActionNotification(
                $teacherUser,
                $student,
                'تم تعديل العلامة',
                sprintf('تم تعديل العلامة الخاصة بـ %s.', $student->getFullNameAttribute()),
                ['type' => 'mark_update']
            ));
        }

        return $this->formatMark($studentMark);
    }

    public function deleteStudentMark(int $teacherId, int $markId): array
    {
        $studentMark = $this->repository->findTeacherMark($markId, $teacherId);

        if (!$studentMark) {
            throw new ModelNotFoundException('العلامة غير موجودة أو لا تملك صلاحية الوصول إليها.');
        }

        $studentMark->delete();

        return [
            'id' => $markId,
            'deleted' => true,
        ];
    }

    protected function validateMarkValue(float $mark, SubjectComponent $component): void
    {
        if ($mark < 0 || $mark > $component->out_of) {
            throw new \Exception(sprintf('العلامة يجب أن تكون بين 0 و %s.', $component->out_of));
        }
    }

    protected function validateUniqueMark(int $enrollmentId, int $subjectId, int $subjectComponentId, int $termId, int $ignoreMarkId = null): void
    {
        $query = StudentMark::query()
            ->where('enrollment_id', $enrollmentId)
            ->where('subject_id', $subjectId)
            ->where('subject_component_id', $subjectComponentId)
            ->where('term_id', $termId);

        if ($ignoreMarkId) {
            $query->where('id', '!=', $ignoreMarkId);
        }

        if ($query->exists()) {
            throw new \Exception('تم إدخال علامة لهذا المكون بالفعل.');
        }
    }

    protected function formatMark(StudentMark $studentMark): array
    {
        return [
            'id' => $studentMark->id,
            'enrollment_id' => $studentMark->enrollment_id,
            'student' => [
                'id' => $studentMark->enrollment->student->id,
                'full_name' => $studentMark->enrollment->student->full_name,
                'user' => $studentMark->enrollment->student->user ? [
                    'id' => $studentMark->enrollment->student->user->id,
                    'name' => $studentMark->enrollment->student->user->name,
                    'phone' => $studentMark->enrollment->student->user->phone,
                ] : null,
            ],
            'section' => $studentMark->enrollment->section ? [
                'id' => $studentMark->enrollment->section->id,
                'name' => $studentMark->enrollment->section->full_name ?? $studentMark->enrollment->section->name ?? null,
            ] : null,
            'subject' => [
                'id' => $studentMark->subject->id,
                'name' => $studentMark->subject->name,
            ],
            'subject_component' => [
                'id' => $studentMark->subjectComponent->id,
                'name' => $studentMark->subjectComponent->type,
                'out_of' => $studentMark->subjectComponent->out_of,
            ],
            'term' => [
                'id' => $studentMark->term->id,
                'name' => $studentMark->term->term_name ?? ($studentMark->term->type ?? null),
                'academic_year' => $studentMark->term->academic_year,
            ],
            'mark' => $studentMark->mark,
            'percentage' => $studentMark->subjectComponent ? round(($studentMark->mark / max(1, $studentMark->subjectComponent->out_of)) * 100, 2) : null,
            'created_at' => $studentMark->created_at?->toDateTimeString(),
            'updated_at' => $studentMark->updated_at?->toDateTimeString(),
        ];
    }
}

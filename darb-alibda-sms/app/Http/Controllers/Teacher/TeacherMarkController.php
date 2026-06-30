<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Requests\Teacher\TeacherMarkRequest;
use App\Services\Teacher\TeacherMarkService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeacherMarkController extends TeacherController
{
    public function __construct(protected TeacherMarkService $service)
    {
    }

    /**
     * ❌ تم حذف دالة index() بالكامل لأنها لم تعد مستخدمة.
     */

    public function students(Request $request)
    {
        $user = $request->user();
        $teacher = $user->teacher;

        if (! $teacher) {
            return $this->forbiddenResponse('المستخدم ليس معلماً.');
        }

        $classId = $request->query('class_id');
        $sectionId = $request->query('section_id');
        $subjectId = $request->query('subject_id');
        $termId = $request->query('term_id');

        $sections = $this->service->getTeacherSectionsWithStudentsAndGrades(
            $teacher->id,
            $classId,
            $sectionId,
            $subjectId,
            $termId
        );

        return $this->successResponse($sections, 'تم جلب الشعب والطلاب والعلامات بنجاح.');
    }

    public function store(TeacherMarkRequest $request)
    {
        $user = $request->user();
        $teacher = $user->teacher;

        if (! $teacher) {
            return $this->forbiddenResponse('المستخدم ليس معلماً.');
        }

        try {
            $mark = $this->service->createStudentMark(
                $teacher->id,
                $request->input('enrollment_id'),
                $request->input('student_id'),
                $request->input('class_id'),
                $request->input('section_id'),
                $request->input('subject_id'),
                $request->input('subject_component_id'),
                $request->input('term_id'),
                $request->input('mark')
            );

            return $this->createdResponse($mark, 'تم إضافة العلامة بنجاح.');
        } catch (ModelNotFoundException $exception) {
            return $this->notFoundResponse($exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(null, $exception->getMessage(), 422);
        }
    }

    public function update(Request $request, int $markId)
    {
        $user = $request->user();
        $teacher = $user->teacher;

        if (! $teacher) {
            return $this->forbiddenResponse('المستخدم ليس معلماً.');
        }

        try {
            // الآن نرسل فقط mark
            $mark = $this->service->updateStudentMarkSimple(
                $teacher->id,
                $markId,
                $request->input('mark')
            );

            return $this->successResponse($mark, 'تم تعديل العلامة بنجاح.');
        } catch (ModelNotFoundException $exception) {
            return $this->notFoundResponse($exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(null, $exception->getMessage(), 422);
        }
    }

    public function destroy(Request $request, int $markId)
    {
        $user = $request->user();
        $teacher = $user->teacher;

        if (! $teacher) {
            return $this->forbiddenResponse('المستخدم ليس معلماً.');
        }

        try {
            $result = $this->service->deleteStudentMark($teacher->id, $markId);

            return $this->successResponse($result, 'تم حذف العلامة بنجاح.');
        } catch (ModelNotFoundException $exception) {
            return $this->notFoundResponse($exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(null, $exception->getMessage(), 422);
        }
    }
}

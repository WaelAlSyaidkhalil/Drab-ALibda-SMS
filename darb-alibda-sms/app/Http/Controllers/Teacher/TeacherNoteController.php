<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Requests\Teacher\SendParentNoteRequest;
use App\Services\Teacher\TeacherNoteService;
use Illuminate\Http\Request;

class TeacherNoteController extends TeacherController
{
    public function __construct(protected TeacherNoteService $service)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return $this->forbiddenResponse('المستخدم ليس معلماً.');
        }

        $notes = $this->service->getTeacherNotesForTeacher($user->id);

        return $this->successResponse($notes, 'تم جلب الملاحظات بنجاح.');
    }

    public function destroy(Request $request, int $noteId)
    {
        $user = $request->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return $this->forbiddenResponse('المستخدم ليس معلماً.');
        }

        try {
            $result = $this->service->deleteTeacherNote($user->id, $noteId);

            return $this->successResponse($result, 'تم حذف الملاحظة بنجاح.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->notFoundResponse('الملاحظة غير موجودة أو ليست مملوكة لك.');
        } catch (\Exception $exception) {
            return $this->errorResponse(null, $exception->getMessage(), 422);
        }
    }

    public function update(Request $request, int $noteId)
    {
        $user = $request->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return $this->forbiddenResponse('المستخدم ليس معلماً.');
        }

        try {
            $result = $this->service->updateTeacherNote(
                $user->id,
                $noteId,
                $request->input('title'),
                $request->input('content'),
                $request->file('attachments', [])
            );

            return $this->successResponse($result, 'تم تعديل الملاحظة بنجاح.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->notFoundResponse('الملاحظة غير موجودة أو ليست مملوكة لك.');
        } catch (\Exception $exception) {
            return $this->errorResponse(null, $exception->getMessage(), 422);
        }
    }

    public function store(SendParentNoteRequest $request)
    {
        $user = $request->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return $this->forbiddenResponse('المستخدم ليس معلماً.');
        }

        try {
            $result = $this->service->sendParentNotes(
                $user->id,
                $teacher->id,
                $request->input('student_ids', []),
                $request->input('section_ids', []),
                $request->input('title'),
                $request->input('content'),
                $request->file('attachments', [])
            );

            return $this->createdResponse($result, 'تم إرسال الملاحظة إلى أولياء الأمور بنجاح.');
        } catch (\Exception $exception) {
            return $this->errorResponse(null, $exception->getMessage(), 422);
        }
    }
}

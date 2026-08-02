<?php

namespace App\Services\Teacher;

use App\Models\Academic\Student;
use App\Models\Communication\TeacherNote;
use App\Notifications\Parent\TeacherActionNotification;
use App\Repositories\Teacher\TeacherNoteRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TeacherNoteService
{
    public function __construct(protected TeacherNoteRepository $repository)
    {
    }

    public function getTeacherNotesForTeacher(int $teacherUserId): array
    {
        return $this->repository->getTeacherNotes($teacherUserId)
            ->map(fn (TeacherNote $note) => [
                'id' => $note->id,
                'teacher_id' => $note->teacher_id,
                'student_id' => $note->student_id,
                'title' => $note->title,
                'content' => $note->content,
                'is_read' => $note->is_read,
                'read_at' => $note->read_at?->toDateTimeString(),
                'created_at' => $note->created_at?->toDateTimeString(),
                'updated_at' => $note->updated_at?->toDateTimeString(),
                'teacher' => $note->teacher ? [
                    'id' => $note->teacher->id,
                    'name' => $note->teacher->name,
                ] : null,
                'student' => $note->student ? [
                    'id' => $note->student->id,
                    'full_name' => $note->student->full_name,
                    'user' => $note->student->user ? [
                        'id' => $note->student->user->id,
                        'name' => $note->student->user->name,
                        'phone' => $note->student->user->phone,
                    ] : null,
                    'parent' => $note->student->parent ? [
                        'id' => $note->student->parent->id,
                        'name' => $note->student->parent->name,
                        'phone' => $note->student->parent->phone,
                    ] : null,
                ] : null,
                'attachments' => $note->attachments->map(fn ($attachment) => [
                    'id' => $attachment->id,
                    'file_name' => $attachment->original_name,
                    'path' => $attachment->path,
                    'mime_type' => $attachment->mime_type,
                    'size' => $attachment->size,
                    'url' => $attachment->getUrlAttribute(),
                ])->values(),
            ])->values()
            ->all();
    }

    public function deleteTeacherNote(int $teacherUserId, int $noteId): array
    {
        $note = TeacherNote::query()
            ->where('id', $noteId)
            ->where('teacher_id', $teacherUserId)
            ->firstOrFail();

        foreach ($note->attachments()->get() as $attachment) {
            if (!empty($attachment->path)) {
                Storage::disk($attachment->disk ?? 'public')->delete($attachment->path);
            }

            $attachment->delete();
        }

        $note->delete();

        return [
            'id' => $noteId,
            'deleted' => true,
        ];
    }

    public function updateTeacherNote(int $teacherUserId, int $noteId, ?string $title, ?string $content, ?array $attachments = []): array
    {
        $note = TeacherNote::query()
            ->where('id', $noteId)
            ->where('teacher_id', $teacherUserId)
            ->firstOrFail();

        $data = [];
        if ($title !== null) {
            $data['title'] = $title;
        }
        if ($content !== null) {
            $data['content'] = $content;
        }

        if (!empty($data)) {
            $note->fill($data);
            $note->save();
        }

        if (!empty($attachments)) {
            foreach ($note->attachments()->get() as $existingAttachment) {
                if (!empty($existingAttachment->path)) {
                    Storage::disk($existingAttachment->disk ?? 'public')->delete($existingAttachment->path);
                }

                $existingAttachment->delete();
            }

            foreach ($attachments as $attachment) {
                if ($attachment instanceof UploadedFile && $attachment->isValid()) {
                    $path = $attachment->store('teacher-notes', 'public');
                    $note->addAttachment([
                        'disk' => 'public',
                        'path' => $path,
                        'original_name' => $attachment->getClientOriginalName(),
                        'mime_type' => $attachment->getMimeType(),
                        'size' => $attachment->getSize(),
                        'type' => $this->detectAttachmentType($attachment),
                        'order' => 0,
                        'created_by' => $teacherUserId,
                    ]);
                }
            }
        }

        return [
            'id' => $note->id,
            'title' => $note->title,
            'content' => $note->content,
            'is_read' => $note->is_read,
            'read_at' => $note->read_at?->toDateTimeString(),
            'attachments' => $note->attachments->map(fn ($attachment) => [
                'id' => $attachment->id,
                'file_name' => $attachment->original_name,
                'path' => $attachment->path,
                'mime_type' => $attachment->mime_type,
                'size' => $attachment->size,
                'url' => $attachment->getUrlAttribute(),
            ])->values(),
        ];
    }

    public function sendParentNotes(
        int $teacherUserId,
        int $teacherModelId,
        array $studentIds,
        array $sectionIds,
        string $title,
        string $content,
        ?array $attachments = []
    ): array {
        $teacherSectionIds = $this->repository->getTeacherSectionIds($teacherModelId)->toArray();

        $invalidSectionIds = array_diff($sectionIds, $teacherSectionIds);
        if (!empty($invalidSectionIds)) {
            throw new \Exception('لا يمكنك إرسال ملاحظات لهذه الشُعب: ' . implode(', ', $invalidSectionIds));
        }

        $sectionStudentIds = [];
        if (!empty($sectionIds)) {
            $sectionStudentIds = $this->repository->getActiveStudentIdsForSections($sectionIds)->toArray();
        }

        $invalidStudentIds = [];
        if (!empty($studentIds)) {
            $availableStudentIds = $this->repository->getActiveStudentIdsForSections($teacherSectionIds)->toArray();
            $invalidStudentIds = array_diff($studentIds, $availableStudentIds);

            if (!empty($invalidStudentIds)) {
                throw new \Exception('لا يمكنك إرسال ملاحظات لهؤلاء الطلاب: ' . implode(', ', $invalidStudentIds));
            }
        }

        $allStudentIds = array_values(array_unique(array_merge($sectionStudentIds, $studentIds)));

        if (empty($allStudentIds)) {
            throw new \Exception('لا يوجد طلاب صالحين لإرسال الملاحظة.');
        }

        $createdNotes = $this->repository->createTeacherNotes($teacherUserId, $allStudentIds, $title, $content);

        $teacherUser = \App\Models\Auth\User::find($teacherUserId);

        foreach ($createdNotes as $note) {
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    if ($attachment instanceof UploadedFile && $attachment->isValid()) {
                        $path = $attachment->store('teacher-notes', 'public');
                        $note->addAttachment([
                            'disk' => 'public',
                            'path' => $path,
                            'original_name' => $attachment->getClientOriginalName(),
                            'mime_type' => $attachment->getMimeType(),
                            'size' => $attachment->getSize(),
                            'type' => $this->detectAttachmentType($attachment),
                            'order' => 0,
                            'created_by' => $teacherUserId,
                        ]);
                    }
                }
            }

            $student = Student::find($note->student_id);
            if ($student && $student->parent) {
                $student->parent->notify(new TeacherActionNotification(
                    $teacherUser,
                    $student,
                    'ملاحظة جديدة من المعلم',
                    sprintf('أرسل المعلم ملاحظة جديدة إلى %s.', $student->getFullNameAttribute()),
                    ['type' => 'note']
                ));
            }
        }

        return [
            'created_count' => count($createdNotes),
            'student_ids' => $allStudentIds,
            'notes' => $createdNotes->map(fn (TeacherNote $note) => [
                'id' => $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'is_read' => $note->is_read,
                'read_at' => $note->read_at?->toDateTimeString(),
                'attachments' => $note->attachments->map(fn ($attachment) => [
                    'id' => $attachment->id,
                    'file_name' => $attachment->original_name,
                    'path' => $attachment->path,
                    'mime_type' => $attachment->mime_type,
                    'size' => $attachment->size,
                    'url' => $attachment->getUrlAttribute(),
                ])->values(),
            ])->values(),
        ];
    }

    protected function detectAttachmentType(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }

        return 'document';
    }
    }


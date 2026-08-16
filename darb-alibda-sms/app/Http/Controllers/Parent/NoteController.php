<?php

namespace App\Http\Controllers\Parent;

use App\Models\Communication\TeacherNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends ParentController
{
    public function index(Request $request): JsonResponse
    {
        $notes = TeacherNote::query()
            ->whereHas('student', function ($query) use ($request) {
                $query->where('parent_id', $request->user()->id);
            })
            ->with([
                'student.user',
                'teacher',
                'attachments',
            ])
            ->latest('created_at')
            ->paginate(15);

        $items = $notes->getCollection()
            ->map(fn (TeacherNote $note) => $this->formatNote($note))
            ->values();

        return $this->successResponse([
            'items' => $items,
            'pagination' => [
                'total' => $notes->total(),
                'count' => $notes->count(),
                'per_page' => $notes->perPage(),
                'current_page' => $notes->currentPage(),
                'last_page' => $notes->lastPage(),
            ],
        ], 'تم جلب الملاحظات بنجاح.');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $note = TeacherNote::query()
            ->whereHas('student', function ($query) use ($request) {
                $query->where('parent_id', $request->user()->id);
            })
            ->with([
                'student.user',
                'teacher',
                'attachments',
            ])
            ->findOrFail($id);

        return $this->successResponse(
            $this->formatNote($note),
            'تم جلب الملاحظة بنجاح.'
        );
    }

    /**
     * تنسيق بيانات الملاحظة لواجهة ولي الأمر.
     */
    protected function formatNote(TeacherNote $note): array
    {
        return [
            'id' => $note->id,

            'student' => $note->student ? [
                'id' => $note->student->id,
                'full_name' => $note->student->full_name,
                'registry_number' => $note->student->registry_number,
            ] : null,

            'title' => $note->title,

            'content' => $note->content,

            'is_read' => $note->is_read,

            'read_at' => $note->read_at?->toDateTimeString(),

            'teacher' => $note->teacher ? [
                'id' => $note->teacher->id,
                'name' => $note->teacher->name,
                'email' => $note->teacher->email,
                'phone' => $note->teacher->phone,
            ] : null,

            'attachments' => $note->attachments
                ->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_name' => $attachment->original_name,
                        'path' => $attachment->path,
                        'mime_type' => $attachment->mime_type,
                        'size' => $attachment->size,
                        'type' => $attachment->type,
                        'url' => $attachment->getUrlAttribute(),
                    ];
                })
                ->values(),

            'created_at' => $note->created_at?->toDateTimeString(),

            'updated_at' => $note->updated_at?->toDateTimeString(),
        ];
    }
}
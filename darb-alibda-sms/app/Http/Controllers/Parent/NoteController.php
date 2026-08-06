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
            ->with(['student.user', 'teacher'])
            ->latest('created_at')
            ->paginate(15);

        $items = $notes->getCollection()->map(function (TeacherNote $note) {
            return [
                'id' => $note->id,
                'student' => [
                    'id' => $note->student?->id,
                    'full_name' => $note->student?->full_name,
                    'registry_number' => $note->student?->registry_number,
                ],
                'title' => $note->title,
                'content' => $note->content,
                'teacher' => $note->teacher ? [
                    'id' => $note->teacher->id,
                    'name' => $note->teacher->name,
                    'email' => $note->teacher->email,
                    'phone' => $note->teacher->phone,
                ] : null,
                'created_at' => $note->created_at?->toDateTimeString(),
                'updated_at' => $note->updated_at?->toDateTimeString(),
            ];
        })->values();

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
            ->with(['student.user', 'teacher'])
            ->findOrFail($id);

        return $this->successResponse([
            'id' => $note->id,
            'student' => [
                'id' => $note->student?->id,
                'full_name' => $note->student?->full_name,
                'registry_number' => $note->student?->registry_number,
            ],
            'title' => $note->title,
            'content' => $note->content,
            'teacher' => $note->teacher ? [
                'id' => $note->teacher->id,
                'name' => $note->teacher->name,
                'email' => $note->teacher->email,
                'phone' => $note->teacher->phone,
            ] : null,
            'created_at' => $note->created_at?->toDateTimeString(),
            'updated_at' => $note->updated_at?->toDateTimeString(),
        ], 'تم جلب الملاحظة بنجاح.');
    }
}

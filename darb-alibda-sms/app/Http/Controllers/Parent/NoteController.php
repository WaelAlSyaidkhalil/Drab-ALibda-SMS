<?php

namespace App\Http\Controllers\Parent;

use App\Models\Communication\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends ParentController
{
    public function index(Request $request): JsonResponse
    {
        $notes = Conversation::query()
            ->where(function ($query) use ($request) {
                $query->where('user1_id', $request->user()->id)
                    ->orWhere('user2_id', $request->user()->id);
            })
            ->latest('created_at')
            ->paginate(15);

        return $this->paginatedResponse($notes, 'تم جلب الملاحظات بنجاح.');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $note = Conversation::query()->findOrFail($id);

        return $this->successResponse([
            'id' => $note->id,
            'title' => $note->subject ?? 'ملاحظة',
            'content' => $note->messages()->latest()->first()?->message,
            'teacher' => $note->user1?->name,
            'student' => null,
            'created_at' => $note->created_at?->toDateTimeString(),
        ], 'تم جلب الملاحظة بنجاح.');
    }
}

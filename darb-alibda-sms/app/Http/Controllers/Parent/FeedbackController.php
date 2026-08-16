<?php

namespace App\Http\Controllers\Parent;

use App\Http\Requests\Parent\ParentComplaintRequest;
use App\Http\Requests\Parent\ParentSuggestionRequest;
use App\Models\Communication\Complaint;
use App\Models\Communication\Suggestion;
use Illuminate\Http\JsonResponse;
use App\Enums\SuggestionStatus;
use App\Enums\ComplaintStatus;

class FeedbackController extends ParentController
{
    public function storeSuggestion(ParentSuggestionRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->isParent()) {
            return $this->forbiddenResponse('فقط أولياء الأمور يمكنهم إرسال الاقتراحات.');
        }

        $suggestion = Suggestion::create([
            'user_id' => $user->id,
            'title' => $request->string('title')->toString(),
            'body' => $request->string('body')->toString(),
            'status' => SuggestionStatus::Pending->value,
            'is_acknowledged' => false,
        ]);

        return $this->createdResponse([
            'id' => $suggestion->id,
            'title' => $suggestion->title,
            'body' => $suggestion->body,
            'status' => $suggestion->status instanceof \BackedEnum ? $suggestion->status->value : $suggestion->status,
            'is_acknowledged' => $suggestion->is_acknowledged,
            'created_at' => $suggestion->created_at?->toDateTimeString(),
        ], 'تم إرسال الاقتراح بنجاح.');
    }

    public function storeComplaint(ParentComplaintRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->isParent()) {
            return $this->forbiddenResponse('فقط أولياء الأمور يمكنهم إرسال الشكاوى.');
        }

        $complaint = Complaint::create([
            'user_id' => $user->id,
            'title' => $request->string('title')->toString(),
            'body' => $request->string('body')->toString(),
            'status' => ComplaintStatus::PENDING->value,
        ]);

        return $this->createdResponse([
            'id' => $complaint->id,
            'title' => $complaint->title,
            'body' => $complaint->body,
            'status' => $complaint->status instanceof \BackedEnum ? $complaint->status->value : $complaint->status,
            'created_at' => $complaint->created_at?->toDateTimeString(),
        ], 'تم إرسال الشكوى بنجاح.');
    }
}

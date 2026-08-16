<?php

namespace App\Http\Controllers\Parent;

use App\Http\Requests\Parent\ParentExcuseRequest;
use App\Models\Academic\Student;
use App\Models\Communication\AbsenceJustification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExcuseRequestController extends ParentController
{
    public function store(ParentExcuseRequest $request): JsonResponse
    {
        $student = Student::findOrFail($request->student_id);
        $this->authorize('view', $student);

        $justification = AbsenceJustification::create([
            'student_id' => $student->id,
            'parent_id' => $request->user()->id,
            'absence_date' => $request->absence_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments/justifications', 'public');
            $justification->addAttachment([
                'disk' => 'public',
                'path' => $path,
                'original_name' => $request->file('attachment')->getClientOriginalName(),
                'mime_type' => $request->file('attachment')->getClientMimeType(),
                'size' => $request->file('attachment')->getSize(),
                'type' => 'file',
                'order' => 1,
                'created_by' => $request->user()->id,
            ]);
        }

        return $this->createdResponse([
            'id' => $justification->id,
            'student_id' => $justification->student_id,
            'status' => $justification->status,
        ], 'تم إرسال طلب العذر بنجاح.');
    }

    public function index(Request $request): JsonResponse
    {
        $requests = AbsenceJustification::query()
            ->where('parent_id', $request->user()->id)
            ->latest('created_at')
            ->get();

        return $this->successResponse($requests->map(fn ($requestItem) => [
            'id' => $requestItem->id,
            'student_id' => $requestItem->student_id,
            'absence_date' => $requestItem->absence_date?->toDateString(),
            'reason' => $requestItem->reason,
            'status' => $requestItem->status,
            'created_at' => $requestItem->created_at?->toDateTimeString(),
        ])->values(), 'تم جلب طلبات العذر بنجاح.');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $justification = AbsenceJustification::query()
            ->where('parent_id', $request->user()->id)
            ->findOrFail($id);

        return $this->successResponse([
            'id' => $justification->id,
            'student_id' => $justification->student_id,
            'absence_date' => $justification->absence_date?->toDateString(),
            'reason' => $justification->reason,
            'status' => $justification->status,
            'created_at' => $justification->created_at?->toDateTimeString(),
        ], 'تم جلب طلب العذر بنجاح.');
    }
}

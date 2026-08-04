<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\StoreComplaintRequest;
use App\Services\Teacher\ComplaintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ComplaintController extends TeacherController
{
    public function __construct(
        protected ComplaintService $complaintService
    ) {
    }

    /**
     * جميع شكاوى المعلم.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $complaints = $this->complaintService
                ->getUserComplaints($request->user()->id);

            return $this->successResponse(
                $complaints,
                'تم جلب الشكاوى بنجاح.'
            );
        } catch (\Exception $exception) {
            return $this->errorResponse(
                null,
                $exception->getMessage(),
                422
            );
        }
    }

    /**
     * تفاصيل شكوى واحدة.
     */
    public function show(int $complaint, Request $request): JsonResponse
    {
        try {
            $data = $this->complaintService
                ->getUserComplaintById(
                    $request->user()->id,
                    $complaint
                );

            return $this->successResponse(
                $data,
                'تم جلب الشكوى بنجاح.'
            );
        } catch (ModelNotFoundException $exception) {
            return $this->notFoundResponse('الشكوى غير موجودة.');
        } catch (\Exception $exception) {
            return $this->errorResponse(
                null,
                $exception->getMessage(),
                422
            );
        }
    }

    /**
     * إنشاء شكوى جديدة.
     */
    public function store(
        StoreComplaintRequest $request
    ): JsonResponse {
        try {
            $complaint = $this->complaintService->create(
                $request->user()->id,
                $request->validated()
            );

            return $this->successResponse(
                $complaint,
                'تم إرسال الشكوى بنجاح.'
            );
        } catch (\Exception $exception) {
            return $this->errorResponse(
                null,
                $exception->getMessage(),
                422
            );
        }
    }
}
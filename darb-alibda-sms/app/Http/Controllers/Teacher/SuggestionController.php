<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\StoreSuggestionRequest;
use App\Services\Teacher\SuggestionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuggestionController extends TeacherController
{
    public function __construct(
        protected SuggestionService $suggestionService
    ) {
    }

    /**
     * جميع اقتراحات المعلم
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $suggestions = $this->suggestionService->index(
                $request->user()->id
            );

            return $this->successResponse(
                $suggestions,
                'تم جلب الاقتراحات بنجاح.'
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
     * عرض اقتراح واحد
     */
    public function show(int $suggestion, Request $request): JsonResponse
    {
        try {
            $data = $this->suggestionService->show(
                $request->user()->id,
                $suggestion
            );

            return $this->successResponse(
                $data,
                'تم جلب الاقتراح بنجاح.'
            );

        } catch (ModelNotFoundException $exception) {

            return $this->notFoundResponse(
                'الاقتراح غير موجود.'
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
     * إرسال اقتراح جديد
     */
    public function store(StoreSuggestionRequest $request): JsonResponse
    {
        try {

            $suggestion = $this->suggestionService->store(
                $request->user()->id,
                $request->validated()
            );

            return $this->successResponse(
                $suggestion,
                'تم إرسال الاقتراح بنجاح.'
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
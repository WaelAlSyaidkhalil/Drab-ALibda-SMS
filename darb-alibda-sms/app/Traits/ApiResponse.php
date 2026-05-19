<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Trait ApiResponse
 *
 * يوفر بناء موحد لاستجابات API في المشروع.
 * يستخدم في Controllers لرفع مستوى الاتساق وDebugging.
 */
trait ApiResponse
{
    /**
     * الرسائل الافتراضية لكل حالة.
     *
     * @return array
     */
    protected function apiMessages(): array
    {
        return [
            'success' => 'تمت العملية بنجاح.',
            'created' => 'تم إنشاء المورد بنجاح.',
            'updated' => 'تم تعديل المورد بنجاح.',
            'deleted' => 'تم حذف المورد بنجاح.',
            'no_content' => 'لم يتم العثور على بيانات.',
            'not_found' => 'الملف غير موجود.',
            'validation_error' => 'تأكد من صحة البيانات المدخلة.',
            'unauthorized' => 'غير مصرح بالدخول.',
            'forbidden' => 'ليس لديك صلاحية لهذه العملية.',
            'server_error' => 'حدث خطأ في الخادم. حاول مرة أخرى لاحقاً.',
            'bad_request' => 'طلب غير صالح.',
            'conflict' => 'تعارض في الحالة الحالية للمورد.',
            'too_many_requests' => 'تم تجاوز الحد المسموح من الطلبات.',
            'unsupported_media_type' => 'نوع الوسائط غير مدعوم.',
            'method_not_allowed' => 'طريقة الطلب غير مسموحة.',
            'unauthenticated' => 'الرجاء تسجيل الدخول أولاً.',
            'rate_limit' => 'الحد الأقصى للطلبات تم الوصول إليه.',
            'maintenance' => 'النظام تحت الصيانة حالياً.',
            'service_unavailable' => 'الخدمة غير متاحة حالياً.',
            'timeout' => 'انتهى وقت الاستجابة. حاول مرة أخرى.',
            'invalid_credentials' => 'بيانات الدخول غير صحيحة.',
            'resource_exists' => 'المورد موجود بالفعل.',
            'resource_locked' => 'المورد مقفل حالياً ولا يمكن تغييره.',
            'payment_required' => 'مطلوب الدفع للوصول لهذه الخدمة.',
            'unsupported_operation' => 'العملية غير مدعومة حالياً.',
        ];
    }

    /**
     * استجابة نجاح عامة.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $status
     * @param array $meta
     * @return JsonResponse
     */
    protected function successResponse(mixed $data = null, string $message = null, int $status = 200, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message ?? $this->apiMessages()['success'],
            'data' => $data,
        ];

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    /**
     * استجابة إنشاء.
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function createdResponse(mixed $data = null, string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message ?? $this->apiMessages()['created'], 201);
    }

    /**
     * استجابة تعديل.
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function updatedResponse(mixed $data = null, string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message ?? $this->apiMessages()['updated'], 200);
    }

    /**
     * استجابة حذف.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function deletedResponse(string $message = null): JsonResponse
    {
        return $this->successResponse(null, $message ?? $this->apiMessages()['deleted'], 200);
    }

    /**
     * استجابة عدم وجود محتوى.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function noContentResponse(string $message = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?? $this->apiMessages()['no_content'],
            'data' => null,
        ], 204);
    }

    /**
     * استجابة خطأ عام.
     *
     * @param mixed $errors
     * @param string|null $message
     * @param int $status
     * @return JsonResponse
     */
    protected function errorResponse(mixed $errors = null, string $message = null, int $status = 400): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message ?? $this->apiMessages()['bad_request'],
            'errors' => $errors,
        ];

        return response()->json($payload, $status);
    }

    /**
     * استجابة تحقق البيانات.
     *
     * @param array $errors
     * @param string|null $message
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, string $message = null): JsonResponse
    {
        return $this->errorResponse([
            'code' => 'VALIDATION_ERROR',
            'messages' => $errors,
        ], $message ?? $this->apiMessages()['validation_error'], 422);
    }

    /**
     * استجابة عدم المصادقة.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = null): JsonResponse
    {
        return $this->errorResponse(null, $message ?? $this->apiMessages()['unauthorized'], 401);
    }

    /**
     * استجابة منع الوصول.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = null): JsonResponse
    {
        return $this->errorResponse(null, $message ?? $this->apiMessages()['forbidden'], 403);
    }

    /**
     * استجابة لمورد غير موجود.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = null): JsonResponse
    {
        return $this->errorResponse(null, $message ?? $this->apiMessages()['not_found'], 404);
    }

    /**
     * استجابة تعارض في الحالة.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function conflictResponse(string $message = null): JsonResponse
    {
        return $this->errorResponse(null, $message ?? $this->apiMessages()['conflict'], 409);
    }

    /**
     * استجابة خطأ داخلي للنظام.
     *
     * @param string|null $message
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function serverErrorResponse(string $message = null, mixed $errors = null): JsonResponse
    {
        return $this->errorResponse($errors, $message ?? $this->apiMessages()['server_error'], 500);
    }

    /**
     * استجابة بيانات مقسمة.
     *
     * @param LengthAwarePaginator $paginator
     * @param string|null $message
     * @return JsonResponse
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = null): JsonResponse
    {
        return $this->successResponse(
            [
                'items' => $paginator->items(),
                'pagination' => [
                    'total' => $paginator->total(),
                    'count' => count($paginator->items()),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'next_page_url' => $paginator->nextPageUrl(),
                    'prev_page_url' => $paginator->previousPageUrl(),
                ],
            ],
            $message ?? $this->apiMessages()['success'],
            200
        );
    }

    /**
     * استجابة مخصصة مع بيانات وميتا.
     *
     * @param mixed $data
     * @param array $meta
     * @param string|null $message
     * @param int $status
     * @return JsonResponse
     */
    protected function customResponse(mixed $data, array $meta = [], string $message = null, int $status = 200): JsonResponse
    {
        return $this->successResponse($data, $message, $status, $meta);
    }
}

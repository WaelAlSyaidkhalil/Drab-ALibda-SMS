<?php

namespace App\Http\Controllers\Parent;

use App\Http\Requests\Parent\ParentLoginRequest;
use App\Http\Requests\Parent\ParentPasswordRequest;
use App\Http\Requests\Parent\ParentProfileRequest;
use App\Http\Resources\Parent\ParentProfileResource;
use App\Models\Communication\SchoolInfo;
use App\Services\Parent\ParentAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthController extends ParentController
{
    public function login(
        ParentLoginRequest $request,
        ParentAuthService $authService
    ): JsonResponse {
        $result = $authService->login(
            $request->validated(),
            $request->ip()
        );

        return $this->successResponse([
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
                'phone' => $result['user']->phone,
                'role' => $result['user']->role?->name,
                'is_active' => $result['user']->is_active,
                'fcm_token' => $result['user']->fcm_token,
                'avatar' => $result['user']->avatar,
            ],
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'تم تسجيل الدخول بنجاح.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'user' => $this->formatParentProfile($request->user()),
        ], 'تم جلب بيانات المستخدم بنجاح.');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->successResponse([
            'user' => $this->formatParentProfile($request->user()),
        ], 'تم جلب الملف الشخصي بنجاح.');
    }

    public function updateProfile(
        ParentProfileRequest $request
    ): JsonResponse {
        $user = $request->user();

        $userData = $request->only([
            'name',
            'phone',
            'email',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')
                ->store('avatars/parents', 'public');

            if (! empty($user->avatar)) {
                $this->deleteOldAvatar($user->avatar);
            }

            $userData['avatar'] = Storage::url($path);
        }

        $user->update($userData);

        return $this->successResponse([
            'user' => $this->formatParentProfile($user),
        ], 'تم تحديث الملف الشخصي بنجاح.');
    }

    public function changePassword(
        ParentPasswordRequest $request,
        ParentAuthService $authService
    ): JsonResponse {
        $authService->changePassword(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            null,
            'تم تغيير كلمة المرور بنجاح.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if (! $token) {
            return $this->errorResponse(
                null,
                'فشل تسجيل الخروج. لا يوجد جلسة فعالة.',
                400
            );
        }

        $token->delete();

        return $this->successResponse(
            null,
            'تم تسجيل الخروج بنجاح.'
        );
    }

    public function supportMessage(): JsonResponse
    {
        $schoolInfo = SchoolInfo::getInfo();

        $schoolName =
            $schoolInfo?->school_name ??
            'مدرسة درب الإبداع الخاصة';

        $schoolPhone =
            $schoolInfo?->phone ??
            '0500000000';

        $schoolEmail =
            $schoolInfo?->email ??
            'info@drabalibda.sa';

        return $this->successResponse([
            'support' => [
                'title' => 'دعم الحساب والاسترجاع',
                'body' => "في حال نسيان كلمة المرور أو رقم الجوال المسجل، يرجى التواصل مع إدارة {$schoolName} للحصول على المساعدة الرسمية واستعادة الحساب.",
                'contact' => [
                    'phone' => $schoolPhone,
                    'email' => $schoolEmail,
                ],
                'instructions' => [
                    'أوقات التواصل' =>
                        'من الأحد إلى الخميس خلال أوقات الدوام الرسمي.',
                    'معلومات إضافية' =>
                        'يرجى تجهيز بيانات الحساب الشخصية عند التواصل مع الإدارة.',
                ],
            ],
        ], 'يرجى التواصل مع الإدارة في حال فقدان بيانات الدخول.');
    }

    private function deleteOldAvatar(
        string $avatarUrl
    ): void {
        $relativePath = preg_replace(
            '#^/storage/#',
            '',
            parse_url($avatarUrl, PHP_URL_PATH)
        );

        if (
            $relativePath &&
            Storage::disk('public')->exists($relativePath)
        ) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    private function formatParentProfile($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role?->name,
            'is_active' => $user->is_active,
            'fcm_token' => $user->fcm_token,
            'avatar' => $user->avatar,
            'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
            'created_at' => $user->created_at?->toDateTimeString(),
            'updated_at' => $user->updated_at?->toDateTimeString(),
        ];
    }
}
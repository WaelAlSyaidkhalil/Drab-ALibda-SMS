<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\TeacherLoginRequest;
use App\Services\Teacher\TeacherAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends TeacherController
{
    public function login(TeacherLoginRequest $request, TeacherAuthService $authService): JsonResponse
    {
        $result = $authService->login($request->validated(), $request->ip());

        return $this->successResponse([
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->full_name,
                'email' => $result['user']->email,
                'phone' => $result['user']->phone,
                'role' => $result['user']->role?->name,
                'is_active' => $result['user']->is_active,
            ],
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'تم تسجيل الدخول بنجاح.');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role?->name,
                'is_active' => $user->is_active,
            ],
        ], 'تم جلب بيانات المستخدم بنجاح.');
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح.');
    }
}

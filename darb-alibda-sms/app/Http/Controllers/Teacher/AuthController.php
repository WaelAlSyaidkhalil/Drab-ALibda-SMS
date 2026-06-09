<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\TeacherLoginRequest;
use App\Http\Requests\Teacher\TeacherUpdateProfileRequest;
use App\Models\Communication\SchoolInfo;
use App\Services\Teacher\TeacherAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                'fcm_token' => $result['user']->fcm_token,
            ],
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'تم تسجيل الدخول بنجاح.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'user' => $this->formatTeacherProfile($request->user()),
        ], 'تم جلب بيانات المستخدم بنجاح.');
    }

    public function supportMessage(): JsonResponse
    {
        $schoolInfo = SchoolInfo::getInfo();
        $schoolName = $schoolInfo?->school_name ?? 'مدرسة درب الإبداع الخاصة';
        $schoolPhone = $schoolInfo?->phone ?? '0500000000';

        $schoolEmail = $schoolInfo?->email ?? 'info@drabalibda.sa';

        return $this->successResponse([
            'support' => [
                'title' => 'دعم الحساب والاسترجاع',
                'body' => "في حال نسيان كلمة المرور أو رقم الجوال المسجل، يرجى التواصل مع إدارة {$schoolName} للحصول على المساعدة الرسمية والمتميزة. فريق الدعم سيساعدك في استعادة حسابك بأسرع وقت ممكن وبالطريقة الأكثر أماناً.",
                'contact' => [
                    'phone' => $schoolPhone,
                    'email' => $schoolEmail,
                ],
                'instructions' => [
                    'أوقات التواصل' => 'من الأحد إلى الخميس خلال أوقات العمل الرسمية.',
                    'معلومات إضافية' => 'يرجى التأكد من تقديم بيانات الحساب الشخصية عند التواصل لتسريع عملية المراجعة والاسترجاع.'
                ],
            ],
        ], 'في حال نسيان بيانات الدخول، يرجى التواصل مع الإدارة لتقديم الدعم والاسترجاع الرسمي.');
    }

    public function updateProfile(TeacherUpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $teacher = $user->teacher;

        $userData = $request->only(['email']);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars/teachers', 'public');

            if (! empty($user->avatar)) {
                $this->deleteOldAvatar($user->avatar);
            }

            $userData['avatar'] = Storage::url($path);
        }

        $user->update($userData);

        if ($teacher) {
            $teacher->update($request->only([
                'address',
                'phone_alt',
                'experience_years',
            ]));
        }

        return $this->successResponse([
            'user' => $this->formatTeacherProfile($user),
        ], 'تم تحديث الملف الشخصي بنجاح.');
    }

    private function deleteOldAvatar(string $avatarUrl): void
    {
        $relativePath = preg_replace('#^/storage/#', '', parse_url($avatarUrl, PHP_URL_PATH));

        if ($relativePath && Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if (! $token) {
            return $this->errorResponse(null, 'فشل تسجيل الخروج. لا يوجد جلسة فعالة.', 400);
        }

        $token->delete();

        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح.');
    }

    private function formatTeacherProfile($user): array
    {
        $teacher = $user->teacher;
        $profile = [
            'id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role?->name,
            'is_active' => $user->is_active,
            'fcm_token' => $user->fcm_token,
            'avatar' => $user->avatar ?? null,
            'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
            'created_at' => $user->created_at?->toDateTimeString(),
            'updated_at' => $user->updated_at?->toDateTimeString(),
            'teacher' => null,
        ];

        if (! $teacher) {
            return $profile;
        }

        $schedules = $teacher->schedules()
            ->with(['subject', 'section.schoolClass'])
            ->get();

        $subjects = $schedules
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn ($subject) => [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code ?? null,
                'description' => $subject->description ?? null,
            ]);

        $sections = $schedules
            ->pluck('section')
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn ($section) => [
                'id' => $section->id,
                'name' => $section->name,
                'grade_level' => $section->grade_level ?? null,
                'school_class' => $section->schoolClass ? [
                    'id' => $section->schoolClass->id,
                    'name' => $section->schoolClass->name,
                    'grade_level' => $section->schoolClass->grade_level,
                ] : null,
            ]);

        $scheduleItems = $schedules->map(fn ($schedule) => [
            'id' => $schedule->id,
            'day' => $schedule->day ?? null,
            'start_time' => $schedule->start_time ?? null,
            'end_time' => $schedule->end_time ?? null,
            'term' => $schedule->term?->name ?? null,
            'subject' => $schedule->subject ? [
                'id' => $schedule->subject->id,
                'name' => $schedule->subject->name,
            ] : null,
            'section' => $schedule->section ? [
                'id' => $schedule->section->id,
                'name' => $schedule->section->name,
                'school_class' => $schedule->section->schoolClass ? [
                    'id' => $schedule->section->schoolClass->id,
                    'name' => $schedule->section->schoolClass->name,
                    'grade_level' => $schedule->section->schoolClass->grade_level,
                ] : null,
            ] : null,
        ])->values();

        $profile['teacher'] = [
            'id' => $teacher->id,
            'national_id' => $teacher->national_id,
            'registry_number' => $teacher->registry_number,
            'specialization' => $teacher->specialization,
            'employee_number' => $teacher->employee_number,
            'hire_date' => $teacher->hire_date?->toDateString(),
            'employment_type' => $teacher->employment_type,
            'grade' => $teacher->grade,
            'address' => $teacher->address,
            'phone_alt' => $teacher->phone_alt,
            'experience_years' => $teacher->experience_years,
            'created_at' => $teacher->created_at?->toDateTimeString(),
            'updated_at' => $teacher->updated_at?->toDateTimeString(),
            'subjects' => $subjects,
            'sections' => $sections,
            'schedules' => $scheduleItems,
        ];

        return $profile;
    }
}

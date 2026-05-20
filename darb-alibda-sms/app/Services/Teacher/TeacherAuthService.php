<?php

namespace App\Services\Teacher;

use App\Repositories\Teacher\TeacherAuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TeacherAuthService
{
    protected const MAX_ATTEMPTS = 5;

    public function __construct(protected TeacherAuthRepository $repository)
    {
    }

    public function login(array $data, string $ip): array
    {
        $this->ensureNotRateLimited($data['phone'], $ip);

        $user = $this->repository->findByPhone($data['phone']);

        if (! $user || ! $user->isTeacher() || ! $user->is_active) {
            RateLimiter::hit($this->throttleKey($data['phone'], $ip));

            throw ValidationException::withMessages([
                'phone' => 'رقم الجوال غير مسجل كمعلم نشط. تأكد من أنك تستخدم بيانات الحساب الصحيحة.',
            ]);
        }

        if (! Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($this->throttleKey($data['phone'], $ip));

            throw ValidationException::withMessages([
                'phone' => 'بيانات الجوال أو كلمة المرور غير صحيحة.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($data['phone'], $ip));

        return [
            'user' => $user,
            'token' => $user->createToken('teacher-api-token')->plainTextToken,
        ];
    }

    public function logoutCurrentToken($user): void
    {
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }

    protected function ensureNotRateLimited(string $phone, string $ip): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($phone, $ip), self::MAX_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'phone' => 'لقد تجاوزت حد محاولات تسجيل الدخول. انتظر دقيقة وحاول مرة أخرى.',
            ]);
        }
    }

    protected function throttleKey(string $phone, string $ip): string
    {
        return Str::transliterate(Str::lower($phone).'|'.$ip);
    }
}

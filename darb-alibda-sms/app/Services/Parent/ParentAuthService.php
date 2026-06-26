<?php

namespace App\Services\Parent;

use App\Events\Parent\ParentLoggedIn;
use App\Events\Parent\ParentLoginFailed;
use App\Repositories\Parent\ParentAuthRepository;
use App\Models\Auth\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ParentAuthService
{
    protected const MAX_ATTEMPTS = 5;

    public function __construct(
        protected ParentAuthRepository $repository
    ) {
    }

    public function login(array $data, string $ip): array
    {
        $phone = $data['phone_number'] ?? $data['phone'];

        $this->ensureNotRateLimited($phone, $ip);

        $user = $this->repository->findByPhone($phone);

        if (! $user || ! $user->isParent() || ! $user->is_active) {
            RateLimiter::hit($this->throttleKey($phone, $ip));

            event(new ParentLoginFailed(
                $phone,
                $ip,
                'parent_not_active_or_not_found'
            ));

            throw ValidationException::withMessages([
                'phone' => 'رقم الجوال غير مسجل كولي أمر نشط. تأكد من أنك تستخدم بيانات الحساب الصحيحة.',
            ]);
        }

        // ✅ التعديل هنا: مقارنة النص العادي بدلاً من Hash::check()
        if ($data['password'] !== $user->password) {
            RateLimiter::hit($this->throttleKey($phone, $ip));

            event(new ParentLoginFailed(
                $phone,
                $ip,
                'invalid_password'
            ));

            throw ValidationException::withMessages([
                'password' => 'كلمة المرور خاطئة.',
            ]);
        }

        RateLimiter::clear(
            $this->throttleKey($phone, $ip)
        );

        if (! empty($data['fcm_token'])) {
            $user->fcm_token = $data['fcm_token'];
            $user->save();
        }

        $token = $user
            ->createToken('parent-api-token')
            ->plainTextToken;

        event(new ParentLoggedIn($user, $ip));

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function changePassword(User $user, array $data): void
    {
        // ✅ التعديل هنا: مقارنة النص العادي بدلاً من Hash::check()
        if ($data['current_password'] !== $user->password) {
            throw ValidationException::withMessages([
                'current_password' => 'كلمة المرور الحالية غير صحيحة.',
            ]);
        }

        $user->update([
            // ✅ التعديل هنا: تخزين النص العادي بدلاً من Hash::make()
            'password' => $data['new_password'],
        ]);
    }

    public function logoutCurrentToken(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }

    protected function ensureNotRateLimited(
        string $phone,
        string $ip
    ): void {
        if (
            RateLimiter::tooManyAttempts(
                $this->throttleKey($phone, $ip),
                self::MAX_ATTEMPTS
            )
        ) {
            throw ValidationException::withMessages([
                'phone' => 'لقد تجاوزت حد محاولات تسجيل الدخول. انتظر دقيقة ثم حاول مرة أخرى.',
            ]);
        }
    }

    protected function throttleKey(
        string $phone,
        string $ip
    ): string {
        return Str::transliterate(
            Str::lower($phone).'|'.$ip
        );
    }
}
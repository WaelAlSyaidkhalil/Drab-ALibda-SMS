<?php

namespace App\Notifications\Teacher;

use App\Models\Auth\User;

class LoginNotification
{
    public User $user;
    public string $ip;

    public function __construct(User $user, string $ip)
    {
        $this->user = $user;
        $this->ip = $ip;
    }

    public function title(): string
    {
        return 'تم تسجيل الدخول';
    }

    public function body(): string
    {
        return sprintf('تم تسجيل الدخول إلى حسابك من عنوان IP: %s.', $this->ip);
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user->id,
            'phone' => $this->user->phone,
            'ip' => $this->ip,
            'message' => $this->body(),
        ];
    }
}

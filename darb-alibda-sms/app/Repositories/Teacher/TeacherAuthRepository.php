<?php

namespace App\Repositories\Teacher;

use App\Models\Auth\User;

class TeacherAuthRepository
{
    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)
            ->with('role')
            ->first();
    }
}

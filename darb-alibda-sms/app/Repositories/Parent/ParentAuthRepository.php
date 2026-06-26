<?php

namespace App\Repositories\Parent;

use App\Models\Auth\User;

class ParentAuthRepository
{
    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)
            ->with('role')
            ->first();
    }
}
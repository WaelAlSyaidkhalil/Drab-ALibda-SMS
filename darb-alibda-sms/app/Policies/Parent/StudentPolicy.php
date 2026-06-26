<?php

namespace App\Policies\Parent;

use App\Models\Academic\Student;
use App\Models\Auth\User;

class StudentPolicy
{
    public function view(User $user, Student $student): bool
    {
        return $user->isParent() && $student->parent_id === $user->id;
    }
}

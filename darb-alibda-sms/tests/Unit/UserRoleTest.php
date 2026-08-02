<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_teacher_is_recognized_when_role_name_is_cast_to_enum(): void
    {
        $user = new User();
        $user->setRelation('role', new Role(['name' => UserRole::TEACHER]));

        $this->assertTrue($user->isTeacher());
    }
}

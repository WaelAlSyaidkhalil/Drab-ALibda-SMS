<?php

namespace Database\Seeders;

use App\Models\Auth\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'مسؤول النظام'],
            ['name' => 'teacher', 'description' => 'المعلم'],
            ['name' => 'student', 'description' => 'الطالب'],
            ['name' => 'parent', 'description' => 'ولي الأمر'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}

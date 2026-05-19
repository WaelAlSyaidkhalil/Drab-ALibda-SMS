<?php

namespace Database\Seeders;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentRole = Role::firstWhere('name', 'parent');

        if (! $parentRole) {
            return;
        }

        User::updateOrCreate(
            ['email' => 'parent1@example.com'],
            [
                'name' => 'ولي الأمر الأول',
                'email' => 'parent1@example.com',
                'phone' => '0503333333',
                'role_id' => $parentRole->id,
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'parent2@example.com'],
            [
                'name' => 'ولي الأمر الثاني',
                'email' => 'parent2@example.com',
                'phone' => '0504444444',
                'role_id' => $parentRole->id,
                'password' => Hash::make('password'),
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'مسؤول النظام']
        );

        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'phone' => '0500000000',
                'role_id' => $role->id,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        $this->command->info('Admin dashboard account created: ' . $user->email . ' / password');
    }
}

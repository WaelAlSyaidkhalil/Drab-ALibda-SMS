<?php

namespace Database\Seeders;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Services\Admin\GeneratePasswordService;
use Illuminate\Database\Seeder;

class ParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(GeneratePasswordService $generatePasswordService): void
    {
        $parentRole = Role::firstWhere('name', 'parent');

        if (! $parentRole) {
            return;
        }

        $parents = [
            ['email' => 'parent1@example.com', 'name' => 'ولي الأمر الأول', 'phone' => '0503333001'],
            ['email' => 'parent2@example.com', 'name' => 'ولي الأمر الثاني', 'phone' => '0503333002'],
            ['email' => 'parent3@example.com', 'name' => 'محمد السعيد', 'phone' => '0503333003'],
            ['email' => 'parent4@example.com', 'name' => 'خالد النجار', 'phone' => '0503333004'],
            ['email' => 'parent5@example.com', 'name' => 'عبدالله الشمري', 'phone' => '0503333005'],
            ['email' => 'parent6@example.com', 'name' => 'سليمان الحربي', 'phone' => '0503333006'],
            ['email' => 'parent7@example.com', 'name' => 'إبراهيم القحطاني', 'phone' => '0503333007'],
            ['email' => 'parent8@example.com', 'name' => 'ياسر الغامدي', 'phone' => '0503333008'],
            ['email' => 'parent9@example.com', 'name' => 'فهد العتيبي', 'phone' => '0503333009'],
            ['email' => 'parent10@example.com', 'name' => 'صالح الدوسري', 'phone' => '0503333010'],
        ];

        foreach ($parents as $parentData) {
            User::updateOrCreate(
                ['email' => $parentData['email']],
                [
                    'name' => $parentData['name'],
                    'email' => $parentData['email'],
                    'phone' => $parentData['phone'],
                    'role_id' => $parentRole->id,
                    'password' => $generatePasswordService->generatePassword(),
                ]
            );
        }
    }
}

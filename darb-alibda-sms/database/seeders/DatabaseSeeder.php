<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SchoolInfoSeeder::class,
            AdminDashboardSeeder::class,
            // AdminSeeder::class,
            TeacherSeeder::class,
            ParentSeeder::class,
            StudentSeeder::class,
            WaleedTeacherScenarioSeeder::class,
            // AcademicGradingSeeder::class,
            // TeacherDashboardSeeder::class,
            TeacherNotificationsSeeder::class,
            ParentNotificationsSeeder::class,
            AbsenceJustificationSeeder::class,
        ]);
    }
}

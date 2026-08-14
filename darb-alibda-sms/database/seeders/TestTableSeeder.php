<?php

namespace Database\Seeders;

use App\Models\TestTable;
use Illuminate\Database\Seeder;

class TestTableSeeder extends Seeder
{
    /**
     * تعبئة الجدول الاختباري بسجلات ثابتة للتحقق من خط النشر.
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'Test One', 'description' => 'أول سجل اختباري', 'is_active' => true],
            ['name' => 'Test Two', 'description' => 'ثاني سجل اختباري', 'is_active' => true],
            ['name' => 'Test Three', 'description' => null, 'is_active' => false],
        ];

        foreach ($rows as $row) {
            TestTable::query()->updateOrCreate(['name' => $row['name']], $row);
        }
    }
}

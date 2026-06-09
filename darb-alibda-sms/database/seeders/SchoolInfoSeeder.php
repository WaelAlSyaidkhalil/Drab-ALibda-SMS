<?php

namespace Database\Seeders;

use App\Models\Communication\SchoolInfo;
use Illuminate\Database\Seeder;

class SchoolInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SchoolInfo::updateOrCreate([], [
            'name' => 'مدرسة درب الإبداع الخاصة',
            'description' => 'مدرسة متخصصة في تقديم تجربة تعليمية حديثة ومتميزة للطلاب والطالبات، تجمع بين المنهج الأكاديمي الراسخ والتربية القيمية الموجهة نحو المستقبل.',
            'address' => 'الرياض، المملكة العربية السعودية',
            'phone' => '0501234567',
            'email' => 'info@drabalibda.sa',
            'website' => 'https://drabalibda-sa.com',
        ]);
    }
}

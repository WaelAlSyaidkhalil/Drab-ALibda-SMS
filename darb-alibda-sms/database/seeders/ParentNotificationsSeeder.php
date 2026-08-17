<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Auth\User;

class ParentNotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $parent = User::whereHas('role', fn($q) => $q->where('name', 'parent'))->first();

        if (! $parent) {
            $this->command->info('No parent user found. Skipping ParentNotificationsSeeder.');
            return;
        }

        $now = Carbon::now()->toDateTimeString();

        $items = [
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $parent->id,
                'data' => json_encode([
                    'title' => 'ملاحظة جديدة من المعلم',
                    'body' => 'أرسل المعلم أحمد ملاحظة جديدة بخصوص تحصيل الطالب.',
                    'from' => 'teacher',
                    'teacher_name' => 'أحمد محمد',
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $parent->id,
                'data' => json_encode([
                    'title' => 'إشعار من الإدارة',
                    'body' => 'يرجى التواصل مع الإدارة بخصوص الرسوم الدراسية.',
                    'from' => 'admin',
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $parent->id,
                'data' => json_encode([
                    'title' => 'تقرير غياب الطالب',
                    'body' => 'لاحظنا غياب الطالب في الحصتين الأخيرتين اليوم.',
                    'from' => 'teacher',
                    'teacher_name' => 'فاطمة علي',
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $parent->id,
                'data' => json_encode([
                    'title' => 'إشعار مهم من الإدارة',
                    'body' => 'انتبه للعطلة القادمة الموافقة لتاريخ 15/9/2026.',
                    'from' => 'admin',
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $parent->id,
                'data' => json_encode([
                    'title' => 'درجات الاختبار الدوري متاحة',
                    'body' => 'تم رفع درجات الاختبار الدوري للغة الإنجليزية. يرجى مراجعة النتائج.',
                    'from' => 'teacher',
                    'teacher_name' => 'سارة محمود',
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('notifications')->insert($items);

        $this->command->info('Inserted ' . count($items) . ' notifications for parent id ' . $parent->id);
    }
}

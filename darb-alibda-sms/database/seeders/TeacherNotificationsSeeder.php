<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Auth\User;

class TeacherNotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::whereHas('role', fn ($q) => $q->where('name', 'teacher'))->first();

        if (! $teacher) {
            $this->command->info('No teacher user found. Skipping TeacherNotificationsSeeder.');
            return;
        }

        $now = Carbon::now()->toDateTimeString();

        $items = [
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $teacher->id,
                'data' => json_encode([
                    'title' => 'تنبيه من الإدارة',
                    'body' => 'يرجى مراجعة سجل الحضور لهذا الأسبوع.',
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
                'notifiable_id' => $teacher->id,
                'data' => json_encode([
                    'title' => 'طلب من ولي أمر',
                    'body' => 'تواصل معي بخصوص مستوى الطالب محمد.',
                    'from' => 'parent',
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $teacher->id,
                'data' => json_encode([
                    'title' => 'إشعار عاجل من الإدارة',
                    'body' => 'هناك اجتماع لمعلمي القسم يوم غد الساعة 10 صباحًا.',
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
                'notifiable_id' => $teacher->id,
                'data' => json_encode([
                    'title' => 'رسالة من ولي أمر',
                    'body' => 'هل يمكن تعديل موعد استلام العمل المنزلي؟',
                    'from' => 'parent',
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $teacher->id,
                'data' => json_encode([
                    'title' => 'تذكير من الإدارة',
                    'body' => 'التقارير نهاية الشهر: يرجى رفع تقييمات الطلاب.',
                    'from' => 'admin',
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('notifications')->insert($items);

        $this->command->info('Inserted ' . count($items) . ' notifications for teacher id ' . $teacher->id);
    }
}

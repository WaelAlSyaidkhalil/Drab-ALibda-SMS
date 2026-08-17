<?php

namespace Database\Seeders;

use App\Models\Communication\AbsenceJustification;
use App\Models\Academic\Student;
use App\Models\Auth\User;
use App\Models\Auth\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AbsenceJustificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some students and parents
        $students = Student::whereHas('parent')->take(3)->get();
        
        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();
        $adminUsers = User::where('role_id', $adminRole?->id)->take(2)->get();

        if ($students->isEmpty()) {
            return;
        }

        $reasons = [
            'مرض الطالب',
            'ظروف عائلية طارئة',
            'موعد طبي مهم',
            'حالة طارئة في المنزل',
            'إجازة عائلية',
        ];

        $reviewNotes = [
            'تم قبول الطلب بعد التحقق من الأسباب',
            'تم رفض الطلب - لم يتم تقديم مستندات داعمة',
            'تم الموافقة بناءً على شهادة طبية',
            'انتظر توضيحات إضافية من الوالد',
            'تم التحقق من الأسباب المقدمة',
        ];

        $recordsData = [
            [
                'status' => 'pending',
                'reason' => $reasons[0],
                'absence_date' => Carbon::now()->subDays(5),
                'review_note' => null,
            ],
            [
                'status' => 'approved',
                'reason' => $reasons[1],
                'absence_date' => Carbon::now()->subDays(10),
                'review_note' => $reviewNotes[0],
            ],
            [
                'status' => 'rejected',
                'reason' => $reasons[2],
                'absence_date' => Carbon::now()->subDays(15),
                'review_note' => $reviewNotes[1],
            ],
            [
                'status' => 'pending',
                'reason' => $reasons[3],
                'absence_date' => Carbon::now()->subDays(3),
                'review_note' => null,
            ],
            [
                'status' => 'approved',
                'reason' => $reasons[4],
                'absence_date' => Carbon::now()->subDays(20),
                'review_note' => $reviewNotes[2],
            ],
            [
                'status' => 'pending',
                'reason' => $reasons[0],
                'absence_date' => Carbon::now()->subDays(1),
                'review_note' => null,
            ],
        ];

        foreach ($recordsData as $index => $data) {
            $student = $students[$index % $students->count()];
            $admin = $adminUsers->isNotEmpty() ? $adminUsers[$index % $adminUsers->count()] : null;

            AbsenceJustification::create([
                'student_id' => $student->id,
                'parent_id' => $student->parent_id,
                'absence_date' => $data['absence_date'],
                'reason' => $data['reason'],
                'status' => $data['status'],
                'review_note' => $data['review_note'],
                'reviewed_by' => $data['status'] !== 'pending' ? $admin?->id : null,
                'reviewed_at' => $data['status'] !== 'pending' ? now() : null,
            ]);
        }
    }
}

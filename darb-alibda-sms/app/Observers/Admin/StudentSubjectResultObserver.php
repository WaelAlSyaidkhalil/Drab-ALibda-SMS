<?php

namespace App\Observers\Admin;

use App\Enums\MarkResult;
use App\Models\Grading\StudentMark;
use App\Models\Grading\StudentSubjectResult;
use App\Models\Subjects\Subject;

class StudentSubjectResultObserver
{
    protected static bool $isRecalculating = false;

    /**
     * عند إنشاء أو تعديل علامة.
     */
    public function saved(StudentMark $studentMark): void
    {
        if (self::$isRecalculating) {
            return;
        }

        self::$isRecalculating = true;

        try {
            $this->recalculate($studentMark);
        } finally {
            self::$isRecalculating = false;
        }
    }

    /**
     * عند حذف علامة.
     */
    public function deleted(StudentMark $studentMark): void
    {
        if (self::$isRecalculating) {
            return;
        }

        self::$isRecalculating = true;

        try {
            $this->recalculate($studentMark);
        } finally {
            self::$isRecalculating = false;
        }
    }

    /**
     * إعادة حساب نتيجة المادة.
     */
    protected function recalculate(StudentMark $studentMark): void
    {
        $enrollmentId = $studentMark->enrollment_id;
        $subjectId = $studentMark->subject_id;

        $subject = Subject::find($subjectId);

        if (! $subject) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | جلب جميع العلامات للمادة والتسجيل
        |--------------------------------------------------------------------------
        */

        $marks = StudentMark::query()
            ->where('enrollment_id', $enrollmentId)
            ->where('subject_id', $subjectId)
            ->with([
                'term',
                'subjectComponent',
            ])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | تجميع علامات الفصل الأول
        |--------------------------------------------------------------------------
        */

        $term1Marks = $marks->filter(
            fn (StudentMark $mark) =>
                $mark->term?->type?->value === 'First_Term'
        );

        /*
        |--------------------------------------------------------------------------
        | تجميع علامات الفصل الثاني
        |--------------------------------------------------------------------------
        */

        $term2Marks = $marks->filter(
            fn (StudentMark $mark) =>
                $mark->term?->type?->value === 'Second_Term'
        );

        /*
        |--------------------------------------------------------------------------
        | حساب الفصل الأول
        |--------------------------------------------------------------------------
        |
        | كل مكون له out_of خاص به.
        |
        | مثال:
        | written  = 35 / 50
        | oral     = 24 / 30
        | practical = 18 / 20
        |
        | مجموع الفصل = 77 / 100
        |
        */

        $term1Mark = $this->calculateTermMark($term1Marks);

        /*
        |--------------------------------------------------------------------------
        | حساب الفصل الثاني
        |--------------------------------------------------------------------------
        */

        $term2Mark = $this->calculateTermMark($term2Marks);

        /*
        |--------------------------------------------------------------------------
        | هل يوجد علامات للفصول؟
        |--------------------------------------------------------------------------
        */

        $hasTerm1 = $term1Marks->isNotEmpty();
        $hasTerm2 = $term2Marks->isNotEmpty();

        /*
        |--------------------------------------------------------------------------
        | حساب العلامة السنوية
        |--------------------------------------------------------------------------
        |
        | لا يتم حسابها إلا عند وجود الفصلين.
        |
        */

        $yearlyMark = null;

        if ($hasTerm1 && $hasTerm2) {
            $yearlyMark = round(
                ($term1Mark + $term2Mark) / 2,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | إنشاء أو تحديث النتيجة
        |--------------------------------------------------------------------------
        */

        $result = StudentSubjectResult::firstOrNew([
            'enrollment_id' => $enrollmentId,
            'subject_id' => $subjectId,
        ]);

        $result->term1_mark = $hasTerm1
            ? $term1Mark
            : null;

        $result->term2_mark = $hasTerm2
            ? $term2Mark
            : null;

        $result->yearly_mark = $yearlyMark;

        /*
        |--------------------------------------------------------------------------
        | تحديد النتيجة
        |--------------------------------------------------------------------------
        */

        if ($yearlyMark === null) {

            $result->result = MarkResult::PENDING;

        } elseif ($yearlyMark >= $subject->pass_mark) {

            $result->result = MarkResult::PASS;

        } else {

            $result->result = MarkResult::FAIL;
        }

        $result->save();
    }

    /**
     * حساب علامة فصل دراسي من مكونات المادة.
     *
     * كل مكون يحافظ على علامته كما هي،
     * ويتم جمع العلامات فقط.
     *
     * مثال:
     *
     * written   = 35 / 50
     * oral      = 24 / 30
     * practical = 18 / 20
     *
     * النتيجة = 77 / 100
     */
    protected function calculateTermMark($marks): float
    {
        return round(
            $marks->sum(function (StudentMark $mark) {
                return $mark->mark;
            }),
            2
        );
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 📋 الغرض من هذا الجدول:
     * يمثّل "كشف علامات" الطالب في مادة محددة خلال سنة دراسية كاملة (فصلين).
     * يتم تحديث هذا الجدول تلقائياً من قِبَل النظام عند تسجيل علامات المكونات
     * في جدول student_marks (كتابي + شفهي + وظائف...).
     *
     * 🔢 طريقة الحساب التلقائية:
     *   - term1_mark = SUM(student_marks.mark) في الفصل الأول لهذه المادة
     *   - term2_mark = SUM(student_marks.mark) في الفصل الثاني لهذه المادة
     *   - yearly_mark = (term1_mark + term2_mark) / 2  (عند اكتمال الفصلين)
     *   - result = pass إذا yearly_mark >= subject.pass_mark، وإلا fail
     */
    public function up(): void
    {
        Schema::create('student_subject_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('enrollment_id')
                  ->constrained('student_enrollments')
                  ->cascadeOnDelete();
                  // التسجيل (يحدد الطالب + الشعبة + السنة الدراسية)

            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->cascadeOnDelete();
                  // المادة

            $table->float('term1_mark')->nullable();
            // علامة المادة الكاملة في الفصل الأول (مجموع المكونات)

            $table->float('term2_mark')->nullable();
            // علامة المادة الكاملة في الفصل الثاني (مجموع المكونات)

            $table->float('yearly_mark')->nullable();
            // العلامة السنوية النهائية = متوسط الفصلين

            $table->enum('result', ['pass', 'fail', 'pending'])->default('pending');
            // نتيجة الطالب في هذه المادة

            $table->timestamps();

            // 🔒 الطالب له سجل واحد فقط لكل مادة في كل سنة دراسية
            $table->unique(['enrollment_id', 'subject_id']);

            // 📈 فهرس للبحث السريع
            $table->index(['enrollment_id', 'result']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_subject_results');
    }
};

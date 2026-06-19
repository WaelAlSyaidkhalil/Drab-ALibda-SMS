<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 📋 الغرض:
     * تخزين علامة الطالب في كل مكوّن من مكونات المادة (كتابي/شفهي/وظائف...)
     * لفصل دراسي معين. النظام لاحقاً يجمع هذه العلامات تلقائياً ويخزن النتيجة
     * في جدول student_subject_results.
     */
    public function up(): void
    {
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();

            // ✅ ربط مباشر بالتسجيل (يحدد الطالب + الشعبة + السنة)
            $table->foreignId('enrollment_id')
                  ->constrained('student_enrollments')
                  ->cascadeOnDelete();

            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->cascadeOnDelete();

            $table->foreignId('subject_component_id')
                  ->constrained('subject_components')
                  ->cascadeOnDelete();
                  // المكوّن (كتابي / شفهي / وظائف...)

            $table->foreignId('term_id')
                  ->constrained('terms')
                  ->cascadeOnDelete();
                  // الفصل الدراسي

            // ✅ علامة عشرية لاستيعاب الكسور (مثلاً 17.5)
            $table->float('mark')->default(0);

            $table->timestamps();

            // 🔒 علامة واحدة فقط لكل (تسجيل + مكوّن + فصل)
            $table->unique(
                ['enrollment_id', 'subject_component_id', 'term_id'],
                'unique_mark_per_component'
            );

            // 📈 فهارس لتسريع الاستعلامات
            $table->index(['enrollment_id', 'subject_id', 'term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_marks');
    }
};

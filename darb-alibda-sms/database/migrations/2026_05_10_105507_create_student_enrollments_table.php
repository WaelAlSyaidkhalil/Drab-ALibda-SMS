<?php

use App\Enums\MarkResult;
use App\Enums\StudentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();

            // 🔗 العلاقات الأساسية
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete();
                  // الطالب

            $table->foreignId('section_id')
                  ->constrained('sections')
                  ->cascadeOnDelete();
                  // الشعبة (يستنتج منها الصف تلقائياً)

            $table->string('academic_year');
                  // السنة الدراسية مثل: "2025-2026"

            // 📊 معلومات التسجيل
            $table->date('enrollment_date');
                  // تاريخ التسجيل في الشعبة

            $table->enum('status', [
                StudentStatus::getValues() // active, graduated, dropped
            ])->default(StudentStatus::ACTIVE);
                  // حالة التسجيل (نشط، تخرج، منقطع)

            // 🏆 النتيجة النهائية للسنة
            $table->enum('final_result', MarkResult::getValues()
            )->default(MarkResult::PENDING);
                  // النتيجة النهائية للسنة (مقبول، راسب، معلق)

            $table->float('final_average')->nullable();
                  // المعدل النهائي للسنة

            $table->text('notes')->nullable();
                  // ملاحظات إدارية

            $table->timestamps();

            // 🔒 قيد فريد: الطالب لا يُسجَّل مرتين في نفس السنة الدراسية
            $table->unique(['student_id', 'academic_year']);

            // 📈 فهارس لتسريع البحث
            $table->index(['student_id', 'status']);
            $table->index('academic_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};

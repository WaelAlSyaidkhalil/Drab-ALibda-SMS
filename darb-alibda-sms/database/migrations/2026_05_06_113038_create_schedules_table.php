<?php

use App\Enums\DayOfWeek;
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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();

            // ✅ ربط الحصة بفترة زمنية ثابتة على مستوى المدرسة
            $table->foreignId('time_slot_id')
                  ->constrained('time_slots')
                  ->cascadeOnDelete();

            $table->enum('day', DayOfWeek::getValues());

            $table->timestamps();

            // 📈 فهرس للأداء
            $table->index(['section_id', 'teacher_id']);

            // 🔒 منع تضارب الجدول الدراسي:

            // 1️⃣ المعلم لا يمكن أن يكون في حصتين في نفس الوقت
            $table->unique(
                ['teacher_id', 'term_id', 'day', 'time_slot_id'],
                'unique_teacher_per_slot'
            );

            // 2️⃣ الشعبة لا يمكن أن يكون لها حصتان في نفس الوقت
            $table->unique(
                ['section_id', 'term_id', 'day', 'time_slot_id'],
                'unique_section_per_slot'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};

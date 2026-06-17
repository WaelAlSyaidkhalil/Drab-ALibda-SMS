<?php

use App\Enums\SubjectComponentType;
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
        Schema::create('subject_components', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي للجزء
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete(); // ربط الجزء بالمادة الرئيسية
            $table->enum('type', SubjectComponentType::getValues()); // اسم الجزء: كتابي - شفهي - عملي - نشاط...
            $table->float('out_of', 10, 2); // الدرجة العليا لهذا الجزء فقط
            $table->integer('order'); // ترتيب الجزء في قائمة الأجزاء
            $table->text('description')->nullable(); // وصف الجزء (اختياري)
            $table->timestamps(); // تاريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_components');
    }
};

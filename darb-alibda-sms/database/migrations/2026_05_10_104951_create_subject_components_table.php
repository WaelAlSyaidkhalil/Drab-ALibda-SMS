<?php

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

    $table->foreignId('subject_id')
          ->constrained('subjects')
          ->cascadeOnDelete(); 
          // ربط الجزء بالمادة الرئيسية

    $table->string('name'); 
    // اسم الجزء: كتابي - شفهي - عملي - نشاط...

    $table->integer('full_mark'); 
    // العلامة الكاملة لهذا الجزء فقط            $table->timestamps();
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

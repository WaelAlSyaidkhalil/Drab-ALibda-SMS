<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Gender;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();

            // معلومات شخصية
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', Gender::getValues());

            // معلومات رسمية
            $table->string('national_id')->unique();
            $table->string('registry_number')->nullable();

            // معلومات وظيفية
            $table->string('employee_number')->unique()->nullable();
            $table->date('hire_date')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('grade')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('experience_years')->default(0);

            // معلومات تواصل
            $table->string('address')->nullable();
            $table->string('phone_alt')->nullable();
            
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};

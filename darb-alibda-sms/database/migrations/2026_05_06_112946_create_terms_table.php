<?php

use App\Enums\TermType;
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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->enum('type', TermType::getValues()); // first_term - second_term
            $table->string('academic_year'); // مثل: 2025-2026
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unique(['type', 'academic_year'], 'terms_type_academic_year_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};

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
        if (! Schema::hasColumn('student_marks', 'subject_component_id') && Schema::hasColumn('student_marks', 'component_id')) {
            Schema::table('student_marks', function (Blueprint $table) {
                $table->renameColumn('component_id', 'subject_component_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('student_marks', 'subject_component_id') && ! Schema::hasColumn('student_marks', 'component_id')) {
            Schema::table('student_marks', function (Blueprint $table) {
                $table->renameColumn('subject_component_id', 'component_id');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_notes', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('content');
            }

            if (!Schema::hasColumn('teacher_notes', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teacher_notes', function (Blueprint $table) {
            $table->dropColumn(['is_read', 'read_at']);
        });
    }
};

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
        // No-op. This migration exists to make rollback/refresh safe by removing
        // foreign keys that point to users before the users table is dropped.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $foreignKeys = [
            ['table' => 'complaints', 'constraint' => 'complaints_user_id_foreign'],
            ['table' => 'conversations', 'constraint' => 'conversations_user1_id_foreign'],
            ['table' => 'conversations', 'constraint' => 'conversations_user2_id_foreign'],
            ['table' => 'messages', 'constraint' => 'messages_sender_id_foreign'],
            ['table' => 'news', 'constraint' => 'news_created_by_foreign'],
            ['table' => 'news_reads', 'constraint' => 'news_reads_user_id_foreign'],
            ['table' => 'suggestions', 'constraint' => 'suggestions_user_id_foreign'],
            ['table' => 'students', 'constraint' => 'students_parent_id_foreign'],
            ['table' => 'students', 'constraint' => 'students_user_id_foreign'],
            ['table' => 'teachers', 'constraint' => 'teachers_user_id_foreign'],
            ['table' => 'attachments', 'constraint' => 'attachments_created_by_foreign'],
            ['table' => 'absence_justifications', 'constraint' => 'absence_justifications_parent_id_foreign'],
            ['table' => 'absence_justifications', 'constraint' => 'absence_justifications_reviewed_by_foreign'],
            ['table' => 'teacher_notes', 'constraint' => 'teacher_notes_teacher_id_foreign'],
        ];

        foreach ($foreignKeys as $fk) {
            if (! Schema::hasTable($fk['table'])) {
                continue;
            }

            $connection = Schema::getConnection();
            $schemaManager = $connection->getDoctrineSchemaManager();

            if (! $schemaManager->tablesExist($fk['table'])) {
                continue;
            }

            $table = $schemaManager->listTableDetails($fk['table']);
            if ($table->hasForeignKey($fk['constraint'])) {
                Schema::table($fk['table'], function (Blueprint $table) use ($fk): void {
                    $table->dropForeign($fk['constraint']);
                });
            }
        }
    }
};

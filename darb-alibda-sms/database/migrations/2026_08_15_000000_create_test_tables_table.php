<?php

use Database\Seeders\TestTableSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        // النشر يشغّل migrate فقط، لذلك نزرع البيانات الاختبارية هنا.
        (new TestTableSeeder())->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('test_tables');
    }
};

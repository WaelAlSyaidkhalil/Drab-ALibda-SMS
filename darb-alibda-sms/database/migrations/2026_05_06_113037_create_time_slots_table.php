<?php

use App\Enums\TimeSlotNumber;
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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();

            $table->enum('period_number', TimeSlotNumber::getValues());
            // رقم الحصة (1, 2, 3, ... حتى 7)

            $table->time('start_time');
            // وقت البداية (مثلاً 08:00)

            $table->time('end_time');
            // وقت النهاية (مثلاً 08:45)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};

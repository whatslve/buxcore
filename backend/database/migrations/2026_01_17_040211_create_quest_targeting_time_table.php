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
        Schema::create('quest_targeting_time', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained()->cascadeOnDelete();
            $table->enum('day_of_week', ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'])->nullable();
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->unsignedInteger('min_interval_minutes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_targeting_time');
    }
};

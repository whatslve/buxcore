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
        Schema::create('quest_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('daily_limit')->default(0);
            $table->unsignedInteger('repeat_after_hours')->nullable();
            $table->unsignedInteger('execution_time_hours')->default(120);
            $table->boolean('white_list_only')->default(false);
            $table->boolean('allow_repeat_before_check')->default(true);
            $table->unsignedInteger('min_experience')->default(0);
            $table->enum('ip_filter_mode', ['none', 'full', 'mask'])->default('none');
            $table->enum('captcha_mode', ['none','always','by_experience'])->default('none');
            $table->unsignedInteger('captcha_experience_threshold')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_limits');
    }
};

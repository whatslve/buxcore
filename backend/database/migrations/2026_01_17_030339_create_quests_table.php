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
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('link');
            $table->string('type');
            $table->text('description');
            $table->enum('check_type', ['manual', 'auto'])->default('manual');
            $table->text('complete_info');
            $table->unsignedBigInteger('quest_type_id');
            $table->boolean('is_adult')->default(false);
            $table->boolean('is_soft_install')->default(false);
            $table->boolean('show_screenshot_help')->default(false);
            $table->unsignedInteger('daily_limit')->default(0);
            $table->unsignedInteger('repeat_after_hours')->nullable();
            $table->unsignedInteger('execution_time_hours')->default(120);
            $table->unsignedInteger('min_experience')->default(0);
            $table->boolean('white_list_only')->default(false);
            $table->boolean('allow_parallel')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quests');
    }
};

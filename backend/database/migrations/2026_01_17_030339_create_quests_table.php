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
            $table->string('type');

            $table->text('description');
            $table->text('complete_info');

            $table->enum('check_type', ['manual', 'automatic'])->default('manual');
            $table->enum('link_selection_mode', ['sequential','random'])->default('sequential');


            $table->unsignedBigInteger('quest_type_id');
            $table->unsignedInteger('daily_limit')->default(0);
            $table->unsignedInteger('repeat_after_hours')->nullable();
            $table->unsignedInteger('execution_time_hours')->default(120);
            $table->unsignedInteger('min_experience')->default(0);

            $table->boolean('white_list_only')->default(false);
            $table->boolean('allow_parallel')->default(false);
            $table->boolean('is_adult')->default(false);
            $table->boolean('is_soft_install')->default(false);
            $table->boolean('show_screenshot_help')->default(false);
            $table->boolean('check_is_proofed_email')->default(false);
            $table->boolean('check_is_proofed_phone')->default(false);


            $table->enum('referral_filter', ['all','my_referrals','no_refer'])->default('all');
            $table->timestamp('start_at')->nullable();

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

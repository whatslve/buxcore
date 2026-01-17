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
        Schema::create('quest_targeting_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained('quests')->onDelete('cascade');
            $table->string('activity_type')->nullable();
            $table->string('marital_status')->nullable();
            $table->enum('gender', ['male','female','all'])->default('all');
            $table->unsignedInteger('age_from')->nullable();
            $table->unsignedInteger('age_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_targeting_profile');
    }
};

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
        Schema::create('quest_targeting_geo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained('quests')->onDelete('cascade');
            $table->string('country')->nullable();
            $table->enum('geo_mode', ['none', 'allow_list', 'block_list'])->default('none');
            $table->string('region')->nullable();
            $table->boolean('allow_vpn')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_targeting_geo');
    }
};

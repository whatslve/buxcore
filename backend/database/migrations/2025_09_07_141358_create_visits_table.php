<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('frame')->default(0);
            $table->enum('time', [5, 10, 15, 20, 30, 60, 80, 100])->default(5);
            $table->enum('status', ['on', 'off', 'ban'])->default('off');
            $table->float('reward')->default(0);
            $table->float('cost')->default(0);
            $table->integer('views')->default(0);
            $table->float('balance')->default(0);
            $table->string('link', 255);
            $table->bigInteger('max_views_per_day')->default(0);
            $table->enum('ip_filter', ['full', 'mask', 'off'])->default('off');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};

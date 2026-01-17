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
        Schema::create('quest_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained('quests')->onDelete('cascade');
            $table->decimal('user_payment', 8, 2);
            $table->decimal('execution_cost', 8, 4);
            $table->boolean('auto_replenish')->default(false);
            $table->decimal('replenish_trigger', 8, 2)->nullable();
            $table->decimal('replenish_amount', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_payment');
    }
};

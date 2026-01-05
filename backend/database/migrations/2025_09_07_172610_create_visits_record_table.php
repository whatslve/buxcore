<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visits_record', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->float('reward')->default(0);
            $table->float('cost')->default(0);
            $table->enum('status', ['started', 'finished'])->default('started');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE visits_record ADD COLUMN ip inet");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // явно удалим колонку (не обязательно — всё равно дропаем таблицу, но симметрично up())
        DB::statement("ALTER TABLE visits_record DROP COLUMN IF EXISTS ip");
        Schema::dropIfExists('visits_record');
    }
};

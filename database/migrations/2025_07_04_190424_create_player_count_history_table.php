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
        Schema::create('player_count_history', function (Blueprint $table) {
            $table->id();
            $table->integer('player_count');
            $table->string('app_id')->default('3454830');
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            // Index for efficient querying
            $table->index(['app_id', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_count_history');
    }
};

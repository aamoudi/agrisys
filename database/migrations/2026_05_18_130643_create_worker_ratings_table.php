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
        Schema::create('worker_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained();
            $table->decimal('total_rating', 4, 2)->default(0.00); // Stores the average rate score (e.g., 8.75)
            $table->unsignedInteger('works_num')->default(0); // Total count of contract completions
            $table->decimal('total_hours', 10, 2)->default(0.00); // Historical cumulative hours worked
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_ratings');
    }
};

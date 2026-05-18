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
        Schema::create('work_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained();
            $table->foreignId('worker_id')->constrained();
            $table->decimal('salary', 10, 2); // Exact payout amount for this specific contract task
            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedTinyInteger('rate_num'); // Performance evaluation constraint (1 to 10 scale)
            $table->decimal('work_hours', 6, 2); // Allows tracking precise fractions of hours (e.g., 40.5 hours)
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_details');
    }
};

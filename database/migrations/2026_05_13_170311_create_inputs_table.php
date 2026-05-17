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
        Schema::create('inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained();
            $table->foreignId('type_cd')->constrained('lookup_children');
            // e.g. manure, npk_fertilizer, pesticide, water, herbicide
            $table->decimal('quantity', 10, 2);
            $table->enum('unit',['kg', 'ton', 'L'])->default('kg');
            $table->decimal('cost', 10, 2);
            $table->date('applied_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inputs');
    }
};

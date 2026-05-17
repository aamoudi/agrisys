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
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('crop_id')->constrained();
            $table->decimal('quantitiy', 10, 2);
            $table->enum('unit', ['kg', 'tons', 'piece']);
            $table->enum('quality_grade', ['A', 'B', 'C', 'D'])->nullable();
            $table->date('harvest_date');
            $table->decimal('revenue', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};

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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->unsignedBigInteger('soil_type_cd')->nullable();
            $table->foreign('soil_type_cd')
                ->references('id')->on('lookup_children');
            // clay, loam, sandy, silt
            $table->decimal('size_hectares', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};

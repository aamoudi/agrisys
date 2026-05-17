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
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('farm_id')->constrained();
            $table->foreignId('variety_cd')
                ->constrained('lookup_children');
            $table->enum('type', ['annual', 'perennial']);
            $table->date('planting_date');
            $table->date('expected_harvest_date')->nullable();
            $table->enum('status', ['growing', 'harvested', 'fallow', 'failed'])
                ->default('growing');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};

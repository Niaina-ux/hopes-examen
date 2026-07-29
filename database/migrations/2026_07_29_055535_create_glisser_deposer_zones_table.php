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
        Schema::create('glisser_deposer_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('glisser_deposer_question_id')->constrained('glisser_deposer_questions')->cascadeOnDelete();
            $table->string('nom_zone')->nullable(); // ohatra "Zone 1" (fanamarihana ho an'ny prof)
            $table->decimal('position_x', 5, 2); // % avy amin'ny sisiny havia (0-100)
            $table->decimal('position_y', 5, 2); // % avy amin'ny sisiny ambony (0-100)
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glisser_deposer_zones');
    }
};

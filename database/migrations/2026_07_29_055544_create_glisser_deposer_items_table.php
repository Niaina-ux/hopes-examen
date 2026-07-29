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
        Schema::create('glisser_deposer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('glisser_deposer_question_id')->constrained('glisser_deposer_questions')->cascadeOnDelete();
            $table->foreignId('glisser_deposer_zone_id')->constrained('glisser_deposer_zones')->cascadeOnDelete(); // ny zone marina an'ity item ity
            $table->string('texte'); // ohatra "Cœur", "Poumon"
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glisser_deposer_items');
    }
};

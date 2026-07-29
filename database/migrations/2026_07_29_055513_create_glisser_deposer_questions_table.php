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
        Schema::create('glisser_deposer_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('glisser_deposer_id')->constrained('glisser_deposer')->cascadeOnDelete();
            $table->text('enonce')->nullable(); // ohatra "Placez chaque élément à sa place sur le schéma"
            $table->string('image'); // ilay sary fototra misy ny zone rehetra
            $table->integer('image_largeur')->nullable(); // habe an'ilay sary (px), ilaina amin'ny fikajiana %
            $table->integer('image_hauteur')->nullable();
            $table->decimal('points', 5, 2)->default(1);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glisser_deposer_questions');
    }
};

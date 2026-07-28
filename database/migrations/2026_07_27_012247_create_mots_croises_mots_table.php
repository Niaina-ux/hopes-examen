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
        Schema::create('mots_croises_mots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mots_croises_id')->constrained('mots_croises')->cascadeOnDelete();
            $table->string('indice');              // ex: "Capitale de la France"
            $table->string('reponse');             // ex: "PARIS" (toujours en majuscule)
            $table->enum('direction', ['horizontal', 'vertical'])->default('horizontal');
            $table->integer('position_x')->default(0); // colonne de départ (0-indexed)
            $table->integer('position_y')->default(0); // ligne de départ (0-indexed)
            $table->integer('numero')->default(1);      // numéro affiché dans la grille (1, 2, 3...)
            $table->decimal('points', 5, 2)->default(1);
            $table->json('positions_lettres_visibles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mots_croises_mots');
    }
};

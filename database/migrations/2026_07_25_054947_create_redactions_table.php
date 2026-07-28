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
        Schema::create('redactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_id')->constrained('examens')->cascadeOnDelete();
            $table->foreignId('categorie_id')->constrained('categories')->cascadeOnDelete();
            $table->string('titre')->nullable();
            $table->text('sujet');
            $table->text('instruction')->nullable(); 
            $table->integer('nombre_mots_min')->nullable();
            $table->integer('nombre_mots_max')->nullable();
            $table->integer('duree_minutes')->nullable();
            $table->decimal('note_totale', 5, 2)->nullable();
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redactions');
    }
};

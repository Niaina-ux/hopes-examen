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
        Schema::create('relier_paires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relier_question_id')->constrained('relier_questions')->cascadeOnDelete();
            $table->string('element_gauche');          // ohatra: "France"
            $table->string('image_gauche')->nullable();
            $table->string('element_droite');          // ohatra: "Paris"
            $table->string('image_droite')->nullable();
            $table->integer('ordre_gauche')->default(0);
            $table->integer('ordre_droite')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relier_paires');
    }
};

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
        Schema::create('image_exercice_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_exercice_id')->constrained('image_exercice')->cascadeOnDelete();
            $table->text('instruction'); // ex: "Détourez cette image"
            $table->string('image'); 
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
        Schema::dropIfExists('image_exercice_question');
    }
};

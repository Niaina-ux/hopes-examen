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
        Schema::create('examen_type_exercice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_id')->constrained('examens')->cascadeOnDelete();
            $table->foreignId('type_exercice_id')->constrained('types_exercice')->cascadeOnDelete();
             $table->integer('ordre')->default(0);
            $table->timestamps();

            $table->unique(['examen_id', 'type_exercice_id']); // miaro duplicate
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examen_type_exercice');
    }
};

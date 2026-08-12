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
        Schema::create('categorie_type_exercice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('type_exercice_id')->constrained('types_exercice')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['categorie_id', 'type_exercice_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorie_type_exercice');
    }
};

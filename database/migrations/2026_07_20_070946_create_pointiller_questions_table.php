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
        Schema::create('pointiller_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pointiller_id')->constrained('pointiller')->cascadeOnDelete();
            $table->text('enonce'); // ohatra "Le [1] web est l'ensemble de [2] parfait."
            $table->string('image')->nullable();
            $table->string('video')->nullable();
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
        Schema::dropIfExists('pointiller_questions');
    }
};

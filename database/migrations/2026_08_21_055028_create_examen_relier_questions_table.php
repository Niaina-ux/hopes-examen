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
        Schema::create('examen_relier_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_id')->constrained('examens')->cascadeOnDelete();
            $table->foreignId('relier_question_id')->constrained('relier_questions')->cascadeOnDelete();
            $table->integer('ordre')->default(0);
            $table->timestamps();

            $table->unique(['examen_id', 'relier_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examen_relier_questions');
    }
};

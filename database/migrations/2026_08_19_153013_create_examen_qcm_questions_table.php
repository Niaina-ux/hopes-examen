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
        Schema::create('examen_qcm_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_id')->constrained('examens')->cascadeOnDelete();
            $table->foreignId('qcm_question_id')->constrained('qcm_questions')->cascadeOnDelete();
            $table->integer('ordre')->default(0); // ordre spécifique dans CET examen (peut différer de la banque)
            $table->timestamps();

            $table->unique(['examen_id', 'qcm_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examen_qcm_questions');
    }
};

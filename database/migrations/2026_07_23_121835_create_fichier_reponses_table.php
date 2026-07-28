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
        Schema::create('fichier_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fichier_question_id')->constrained('fichier_questions')->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('fichier_etudiant')->nullable();
            $table->decimal('points_obtenus', 5, 2)->nullable();
            $table->text('commentaire_prof')->nullable();
            $table->boolean('est_corrige')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fichier_reponses');
    }
};

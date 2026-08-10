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
        Schema::create('commentaires', function (Blueprint $table) {
            $table->id();

            // Polymorphic — mifamatotra amin'ny exercice karazany rehetra (Qcm, Code, Text, Redaction, ImageExercice, Fichier, Pointiller, Relier, ...)
            $table->unsignedBigInteger('commentable_id');
            $table->string('commentable_type');

            // Context — examen sy attempt mifandraika
            $table->foreignId('examen_id')->constrained('examens')->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();

            // Auteur — ny prof no manoratra
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->text('contenu');

            $table->timestamps();

            $table->index(['commentable_type', 'commentable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};

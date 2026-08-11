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
        Schema::create('text_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('text_question_id')->constrained('text_questions')->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->longText('reponse_texte');
            $table->longText('reponse_annotee')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('note_obtenue', 5, 2)->nullable(); // NULL mandra-pahavitan'ny correction manuel
            $table->text('commentaire_prof')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_reponses');
    }
};

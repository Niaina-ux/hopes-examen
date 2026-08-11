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
        Schema::create('code_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_question_id')->constrained('code_questions')->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->longText('code_soumis'); 
            $table->longText('code_annote')->nullable();
            $table->decimal('points_obtenus', 5, 2)->nullable(); // ✅ null = mbola tsy notsimbina
            $table->text('commentaire_prof')->nullable(); // fanamarihan'ny mpampianatra
            $table->boolean('est_corrige')->default(false); // ✅ manamarina raha efa nozahan'ny prof
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_reponses');
    }
};

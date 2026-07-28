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
        Schema::create('pointiller_etudiant_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pointiller_reponse_id')->constrained('pointiller_reponses')->cascadeOnDelete(); // <-- ovaina
            $table->foreignId('exam_attempt_id')->nullable()->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('reponse_donnee')->nullable();
            $table->boolean('est_correcte')->default(false);
            $table->decimal('points_obtenus', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointiller_etudiant_reponses');
    }
};

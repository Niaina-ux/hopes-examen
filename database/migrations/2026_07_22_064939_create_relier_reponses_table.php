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
        Schema::create('relier_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relier_paire_id')->constrained('relier_paires')->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            // ✅ ny "droite" nofidin'ny mpianatra ho an'ity "gauche" ity (mifandray amin'ny relier_paires hafa)
            $table->foreignId('paire_choisie_id')->nullable()->constrained('relier_paires')->nullOnDelete();
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
        Schema::dropIfExists('relier_reponses');
    }
};

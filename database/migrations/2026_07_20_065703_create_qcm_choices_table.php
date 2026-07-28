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
        Schema::create('qcm_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qcm_question_id')->constrained('qcm_questions')->cascadeOnDelete();
            $table->string('texte');
            $table->boolean('est_correcte')->default(false);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qcm_choices');
    }
};

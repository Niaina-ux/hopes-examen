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
        Schema::create('pointiller_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pointiller_question_id')->constrained('pointiller_questions')->cascadeOnDelete();
            $table->integer('position');
            $table->string('reponse_correcte');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointiller_reponses');
    }
};

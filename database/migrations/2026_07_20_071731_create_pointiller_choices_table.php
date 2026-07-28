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
        Schema::create('pointiller_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pointiller_reponse_id')->constrained('pointiller_reponses')->cascadeOnDelete();
            $table->string('texte');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointiller_choices');
    }
};

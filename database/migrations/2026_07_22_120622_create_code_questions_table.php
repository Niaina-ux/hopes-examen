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
        Schema::create('code_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_id')->constrained('code')->cascadeOnDelete();
            $table->text('instruction');
            $table->string('langage')->default('Ensemble');
            $table->text('code_starter')->nullable();
            $table->decimal('points', 5, 2)->default(1);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_questions');
    }
};

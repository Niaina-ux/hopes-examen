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
        Schema::create('glisser_deposer_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('glisser_deposer_item_id')->constrained('glisser_deposer_items')->cascadeOnDelete();
            $table->foreignId('glisser_deposer_zone_id')->nullable()->constrained('glisser_deposer_zones')->nullOnDelete(); // ny zone NOTONDROIN'NY student
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
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
        Schema::dropIfExists('glisser_deposer_reponses');
    }
};

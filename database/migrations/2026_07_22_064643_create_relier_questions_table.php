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
        Schema::create('relier_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relier_id')->constrained('relier')->cascadeOnDelete();
            $table->text('enonce')->nullable(); // ohatra "Reliez chaque pays à sa capitale"
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
        Schema::dropIfExists('relier_questions');
    }
};

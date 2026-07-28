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
        Schema::create('types_exercice', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // qcm, relier, code, completer...
            $table->string('slug')->unique(); // "qcm", "relier", "code", "completer" (ho an'ny logique amin'ny code)
            $table->string('icone')->nullable(); // ohatra "fa-solid fa-list-check"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types_exercice');
    }
};

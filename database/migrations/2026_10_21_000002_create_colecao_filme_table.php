<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colecao_filme', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colecao_id')->constrained('colecoes')->cascadeOnDelete();
            $table->foreignId('filme_id')->constrained('filmes')->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->unique(['colecao_id', 'filme_id']);
            $table->unique(['colecao_id', 'position']);
            $table->index(['filme_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colecao_filme');
    }
};

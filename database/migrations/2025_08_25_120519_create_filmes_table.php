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
        Schema::create('filmes', function (Blueprint $table) {
            $table->id(); // chave primária
            $table->string('nome'); // Nome do filme
            $table->text('descricao')->nullable(); // Descrição do filme
            $table->string('plataforma')->nullable(); // Ex: Netflix, Prime, etc
            $table->date('data_assistida')->nullable(); // Data em que assistiu
            $table->string('diretor')->nullable(); // Diretor
            $table->string('genero')->nullable(); // Gênero do filme
            $table->decimal('nota', 3, 1)->nullable(); // Nota (ex: 8.5)
            $table->text('comentarios')->nullable(); // Comentários adicionais
            $table->string('poster')->nullable(); // Caminho da imagem/poster
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filmes');
    }
};

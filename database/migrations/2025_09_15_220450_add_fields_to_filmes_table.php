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
        Schema::table('filmes', function (Blueprint $table) {
            $table->boolean('favorito')->default(false);
            $table->string('generos')->nullable();
            $table->integer('ano_lancamento')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('filmes', function (Blueprint $table) {
            $table->dropColumn(['favorito', 'generos', 'ano_lancamento']);
        });
    }

};

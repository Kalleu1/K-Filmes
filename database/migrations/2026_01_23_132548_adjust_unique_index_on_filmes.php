<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('filmes', function (Blueprint $table) {
            // remove o unique antigo
            $table->dropUnique('filmes_tmdb_id_unique');

            // cria o unique correto
            $table->unique(['user_id', 'tmdb_id']);
        });
    }

    public function down()
    {
        Schema::table('filmes', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'tmdb_id']);
            $table->unique('tmdb_id');
        });
    }
};


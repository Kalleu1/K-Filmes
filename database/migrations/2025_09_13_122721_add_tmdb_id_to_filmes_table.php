<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('filmes', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable()->after('id')->unique()->index();
        });
    }

    public function down(): void
    {
        Schema::table('filmes', function (Blueprint $table) {
            $table->dropColumn('tmdb_id');
        });
    }
};

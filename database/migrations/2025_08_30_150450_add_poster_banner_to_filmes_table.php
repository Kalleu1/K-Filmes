<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filmes', function (Blueprint $table) {
            $table->string('poster_banner')->nullable()->after('poster');
        });
    }

    public function down(): void
    {
        Schema::table('filmes', function (Blueprint $table) {
            $table->dropColumn('poster_banner');
        });
    }
};


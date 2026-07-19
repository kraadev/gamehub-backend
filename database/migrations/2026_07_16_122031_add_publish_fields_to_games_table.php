<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {

            $table->string('developer')->nullable()->after('description');

            $table->string('publisher')->nullable()->after('developer');

            $table->string('game_file')->nullable()->after('banner');

        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {

            $table->dropColumn([
                'developer',
                'publisher',
                'game_file'
            ]);

        });
    }
};
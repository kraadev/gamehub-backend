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
       Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->enum('type', ['html', 'exe']);
            $table->string('engine')->nullable();
            $table->string('platform')->default('Web');

            $table->string('version')->nullable();
            $table->string('size')->nullable();

            $table->string('thumbnail')->nullable();
            $table->string('banner')->nullable();

            $table->string('play_url')->nullable();
            $table->string('download_url')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};

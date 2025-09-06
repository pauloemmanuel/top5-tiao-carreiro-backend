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
        Schema::create('musicas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->bigInteger('visualizacoes')->default(0);
            $table->string('youtube_id')->unique();
            $table->string('thumb');
            $table->enum('status', ['ativa', 'inativa'])->default('ativa');
            $table->timestamps();
            
            $table->index(['status', 'visualizacoes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musicas');
    }
};

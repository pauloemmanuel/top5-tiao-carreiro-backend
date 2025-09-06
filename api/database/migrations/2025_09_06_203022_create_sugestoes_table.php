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
        Schema::create('sugestoes', function (Blueprint $table) {
            $table->id();
            $table->string('url_youtube');
            $table->string('youtube_id')->unique();
            $table->string('titulo')->nullable();
            $table->bigInteger('visualizacoes')->nullable();
            $table->string('thumb')->nullable();
            $table->enum('status', ['pendente', 'aprovada', 'rejeitada'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->string('ip_origem')->nullable();
            $table->foreignId('aprovado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('aprovado_em')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sugestoes');
    }
};

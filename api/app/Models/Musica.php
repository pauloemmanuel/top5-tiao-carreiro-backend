<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Musica extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'visualizacoes',
        'youtube_id',
        'thumb',
        'status'
    ];

    protected $casts = [
        'visualizacoes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para buscar apenas músicas ativas
     */
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('status', 'ativa');
    }

    /**
     * Scope para ordenar por visualizações
     */
    public function scopeOrdenadaPorVisualizacoes(Builder $query): Builder
    {
        return $query->orderBy('visualizacoes', 'desc');
    }

    /**
     * Scope para o top 5
     */
    public function scopeTop5(Builder $query): Builder
    {
        return $query->ativas()
                    ->ordenadaPorVisualizacoes()
                    ->limit(5);
    }

    /**
     * Scope para as demais músicas (6ª em diante)
     */
    public function scopeDemais(Builder $query): Builder
    {
        return $query->ativas()
                    ->ordenadaPorVisualizacoes()
                    ->skip(5);
    }

    /**
     * Formato das visualizações para exibição
     */
    public function getVisualizacoesFormatadaAttribute(): string
    {
        $numero = $this->visualizacoes;
        
        if ($numero >= 1000000000) {
            return number_format($numero / 1000000000, 1) . 'B';
        }
        if ($numero >= 1000000) {
            return number_format($numero / 1000000, 1) . 'M';
        }
        if ($numero >= 1000) {
            return number_format($numero / 1000, 1) . 'K';
        }
        
        return (string) $numero;
    }

    /**
     * URL do YouTube
     */
    public function getYoutubeUrlAttribute(): string
    {
        return "https://www.youtube.com/watch?v={$this->youtube_id}";
    }
}

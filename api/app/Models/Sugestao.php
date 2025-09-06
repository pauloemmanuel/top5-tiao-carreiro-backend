<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Sugestao extends Model
{
    use HasFactory;

    protected $table = 'sugestoes';

    protected $fillable = [
        'url_youtube',
        'youtube_id',
        'titulo',
        'visualizacoes',
        'thumb',
        'status',
        'observacoes',
        'ip_origem',
        'aprovado_por',
        'aprovado_em'
    ];

    protected $casts = [
        'visualizacoes' => 'integer',
        'aprovado_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com usuário que aprovou
     */
    public function aprovadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    /**
     * Scope para sugestões pendentes
     */
    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', 'pendente');
    }

    /**
     * Scope para sugestões aprovadas
     */
    public function scopeAprovadas(Builder $query): Builder
    {
        return $query->where('status', 'aprovada');
    }

    /**
     * Scope para sugestões rejeitadas
     */
    public function scopeRejeitadas(Builder $query): Builder
    {
        return $query->where('status', 'rejeitada');
    }

    /**
     * Extrai o ID do vídeo do YouTube da URL
     */
    public static function extrairVideoId(string $url): ?string
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([^&]+)/',
            '/youtu\.be\/([^?]+)/',
            '/youtube\.com\/embed\/([^?]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Aprovar sugestão
     */
    public function aprovar(User $user, ?string $observacoes = null): bool
    {
        $this->update([
            'status' => 'aprovada',
            'aprovado_por' => $user->id,
            'aprovado_em' => now(),
            'observacoes' => $observacoes
        ]);

        return true;
    }

    /**
     * Rejeitar sugestão
     */
    public function rejeitar(User $user, ?string $observacoes = null): bool
    {
        $this->update([
            'status' => 'rejeitada',
            'aprovado_por' => $user->id,
            'aprovado_em' => now(),
            'observacoes' => $observacoes
        ]);

        return true;
    }

    /**
     * Converte sugestão aprovada em música
     */
    public function converterParaMusica(): ?Musica
    {
        if ($this->status !== 'aprovada') {
            return null;
        }

        return Musica::create([
            'titulo' => $this->titulo,
            'visualizacoes' => $this->visualizacoes,
            'youtube_id' => $this->youtube_id,
            'thumb' => $this->thumb,
            'status' => 'ativa'
        ]);
    }
}

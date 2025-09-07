<?php

namespace App\Services;

use App\Repositories\Interface\SugestaoRepositoryInterface;
use App\Models\Sugestao;
use App\Models\User;
use App\Models\Musica;
use App\Services\YouTubeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SugestaoService
{
    private SugestaoRepositoryInterface $repo;
    private YouTubeService $youtubeService;

    public function __construct(SugestaoRepositoryInterface $repo, YouTubeService $youtubeService)
    {
        $this->repo = $repo;
        $this->youtubeService = $youtubeService;
    }

    public function index(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $status);
    }

    public function store(array $data, string $ipOrigem): Sugestao
    {
        $videoId = $this->youtubeService::extrairVideoId($data['url_youtube']);
        if (!$videoId) {
            throw ValidationException::withMessages([
                'url_youtube' => 'URL do YouTube inválida'
            ]);
        }

        // Verificar se música já existe
        if (Musica::where('youtube_id', $videoId)->exists()) {
            throw ValidationException::withMessages([
                'url_youtube' => 'Esta música já está cadastrada'
            ]);
        }

        // Verificar se sugestão já existe (exceto rejeitadas)
        if ($this->repo->existsByYoutubeId($videoId, ['rejeitada'])) {
            throw ValidationException::withMessages([
                'url_youtube' => 'Esta música já foi sugerida'
            ]);
        }

        // Tentar obter informações do vídeo
        try {
            $videoInfo = $this->youtubeService->getVideoInfo($videoId);
        } catch (\Exception $e) {
            $videoInfo = [
                'titulo' => null,
                'visualizacoes' => null,
                'thumb' => null
            ];
        }

        return $this->repo->create([
            'url_youtube' => $data['url_youtube'],
            'youtube_id' => $videoId,
            'titulo' => $videoInfo['titulo'],
            'visualizacoes' => $videoInfo['visualizacoes'],
            'thumb' => $videoInfo['thumb'],
            'ip_origem' => $ipOrigem,
            'status' => 'pendente'
        ]);
    }

    public function show(int $id): ?Sugestao
    {
        return $this->repo->find($id);
    }

    public function aprovar(Sugestao $sugestao, User $user, ?string $observacoes = null): array
    {
        if ($sugestao->status !== 'pendente') {
            throw ValidationException::withMessages([
                'sugestao' => 'Esta sugestão já foi processada'
            ]);
        }

        // Atualizar informações do vídeo se necessário
        if (!$sugestao->titulo) {
            try {
                $videoInfo = $this->youtubeService->getVideoInfo($sugestao->youtube_id);
                $this->repo->update($sugestao, [
                    'titulo' => $videoInfo['titulo'],
                    'visualizacoes' => $videoInfo['visualizacoes'],
                    'thumb' => $videoInfo['thumb']
                ]);
                $sugestao->refresh();
            } catch (\Exception $e) {
                Log::warning('Não foi possível atualizar informações do vídeo: ' . $e->getMessage());
            }
        }

        $this->repo->aprovar($sugestao, $user, $observacoes);
        $musica = $this->repo->converterParaMusica($sugestao);

        return [
            'sugestao' => $sugestao->fresh(),
            'musica' => $musica
        ];
    }

    public function rejeitar(Sugestao $sugestao, User $user, ?string $observacoes = null): void
    {
        if ($sugestao->status !== 'pendente') {
            throw ValidationException::withMessages([
                'sugestao' => 'Esta sugestão já foi processada'
            ]);
        }

        $this->repo->rejeitar($sugestao, $user, $observacoes);
    }

    public function delete(Sugestao $sugestao): bool
    {
        return $this->repo->delete($sugestao);
    }
}

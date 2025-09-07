<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sugestao;
use App\Models\Musica;
use App\Services\YouTubeService;
use App\Http\Requests\Sugestao\StoreSugestaoRequest;
use App\Http\Requests\Sugestao\ProcessarSugestaoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SugestaoController extends Controller
{
    private YouTubeService $youtubeService;

    public function __construct(YouTubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status');

        $query = Sugestao::with('aprovadoPor')->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $sugestoes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sugestoes->items(),
            'pagination' => [
                'current_page' => $sugestoes->currentPage(),
                'per_page' => $sugestoes->perPage(),
                'total' => $sugestoes->total(),
                'last_page' => $sugestoes->lastPage(),
            ]
        ]);
    }

    public function store(StoreSugestaoRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $videoId = $this->youtubeService::extrairVideoId($request->url_youtube);
            if (!$videoId) {
                throw ValidationException::withMessages([
                    'url_youtube' => 'URL do YouTube inválida'
                ]);
            }

            if (Musica::where('youtube_id', $videoId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta música já está cadastrada'
                ], 409);
            }

            if (Sugestao::where('youtube_id', $videoId)->where('status', '!=', 'rejeitada')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta música já foi sugerida'
                ], 409);
            }

            try {
                $videoInfo = $this->youtubeService->getVideoInfo($videoId);
            } catch (\Exception $e) {
                $videoInfo = [
                    'titulo' => null,
                    'visualizacoes' => null,
                    'youtube_id' => $videoId,
                    'thumb' => null
                ];
            }

            $sugestao = Sugestao::create([
                'url_youtube' => $request->url_youtube,
                'youtube_id' => $videoId,
                'titulo' => $videoInfo['titulo'],
                'visualizacoes' => $videoInfo['visualizacoes'],
                'thumb' => $videoInfo['thumb'],
                'ip_origem' => $request->ip(),
                'status' => 'pendente'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sugestão enviada com sucesso! Aguarde a aprovação.',
                'data' => $sugestao
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao criar sugestão: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function show(Sugestao $sugestao): JsonResponse
    {
        $sugestao->load('aprovadoPor');

        return response()->json([
            'success' => true,
            'data' => $sugestao
        ]);
    }

    public function aprovar(ProcessarSugestaoRequest $request, Sugestao $sugestao): JsonResponse
    {
        try {
            if ($sugestao->status !== 'pendente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta sugestão já foi processada'
                ], 409);
            }

            $validated = $request->validated();

            if (!$sugestao->titulo) {
                try {
                    $videoInfo = $this->youtubeService->getVideoInfo($sugestao->youtube_id);
                    $sugestao->update([
                        'titulo' => $videoInfo['titulo'],
                        'visualizacoes' => $videoInfo['visualizacoes'],
                        'thumb' => $videoInfo['thumb']
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Não foi possível atualizar informações do vídeo: ' . $e->getMessage());
                }
            }

            $sugestao->aprovar($request->user(), $request->observacoes);

            $musica = $sugestao->converterParaMusica();

            return response()->json([
                'success' => true,
                'message' => 'Sugestão aprovada e música adicionada com sucesso',
                'data' => [
                    'sugestao' => $sugestao->fresh(),
                    'musica' => $musica
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao aprovar sugestão: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function rejeitar(ProcessarSugestaoRequest $request, Sugestao $sugestao): JsonResponse
    {
        try {
            if ($sugestao->status !== 'pendente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta sugestão já foi processada'
                ], 409);
            }

            $validated = $request->validated();

            $sugestao->rejeitar($request->user(), $request->observacoes);

            return response()->json([
                'success' => true,
                'message' => 'Sugestão rejeitada com sucesso',
                'data' => $sugestao->fresh()
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao rejeitar sugestão: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function destroy(Sugestao $sugestao): JsonResponse
    {
        try {
            $sugestao->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sugestão removida com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao remover sugestão: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}

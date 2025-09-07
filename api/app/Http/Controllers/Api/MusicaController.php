<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Musica;
use App\Services\YouTubeService;
use App\Services\MusicaService;
use App\Http\Requests\Musica\StoreMusicaRequest;
use App\Http\Requests\Musica\UpdateMusicaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class MusicaController extends Controller
{
    private YouTubeService $youtubeService;
    private MusicaService $musicaService;

    public function __construct(YouTubeService $youtubeService, MusicaService $musicaService)
    {
        $this->youtubeService = $youtubeService;
        $this->musicaService = $musicaService;
        $this->middleware('auth:sanctum')->except(['index', 'show', 'top5', 'demais']);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);

        $musicas = $this->musicaService->index($perPage);

        return response()->json([
            'success' => true,
            'data' => $musicas->items(),
            'pagination' => [
                'current_page' => $musicas->currentPage(),
                'per_page' => $musicas->perPage(),
                'total' => $musicas->total(),
                'last_page' => $musicas->lastPage(),
            ]
        ]);
    }

    public function top5(): JsonResponse
    {
        $musicas = $this->musicaService->top5();

        return response()->json([
            'success' => true,
            'data' => $musicas
        ]);
    }

    public function demais(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);

        $musicas = $this->musicaService->demais($perPage);

        return response()->json([
            'success' => true,
            'data' => $musicas->items(),
            'pagination' => [
                'current_page' => $musicas->currentPage(),
                'per_page' => $musicas->perPage(),
                'total' => $musicas->total(),
                'last_page' => $musicas->lastPage(),
            ]
        ]);
    }

    public function store(StoreMusicaRequest $request): JsonResponse
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

            $videoInfo = $this->youtubeService->getVideoInfo($videoId);

            $musica = $this->musicaService->store($videoInfo);

            return response()->json([
                'success' => true,
                'message' => 'Música adicionada com sucesso',
                'data' => $musica
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar música: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Musica $musica): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $musica
        ]);
    }

    public function update(UpdateMusicaRequest $request, Musica $musica): JsonResponse
    {
        try {
            $validated = $request->validated();

            $data = $request->only(['titulo', 'visualizacoes', 'status']);

            if ($request->has('url_youtube')) {
                $videoId = $this->youtubeService::extrairVideoId($request->url_youtube);
                if (!$videoId) {
                    throw ValidationException::withMessages([
                        'url_youtube' => 'URL do YouTube inválida'
                    ]);
                }

                $existing = Musica::where('youtube_id', $videoId)
                    ->where('id', '!=', $musica->id)
                    ->exists();

                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Esta música já está cadastrada'
                    ], 409);
                }

                $data['youtube_id'] = $videoId;
                $data['thumb'] = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
            }

            $updated = $this->musicaService->update($musica, $data);

            return response()->json([
                'success' => true,
                'message' => 'Música atualizada com sucesso',
                'data' => $updated
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar música: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function destroy(Musica $musica): JsonResponse
    {
        try {
            $this->musicaService->delete($musica);

            return response()->json([
                'success' => true,
                'message' => 'Música removida com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao remover música: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Musica;
use App\Services\YouTubeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class MusicaController extends Controller
{
    private YouTubeService $youtubeService;

    public function __construct(YouTubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
        $this->middleware('auth:sanctum')->except(['index', 'show', 'top5', 'demais']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        
        $musicas = Musica::ativas()
            ->ordenadaPorVisualizacoes()
            ->paginate($perPage);

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

    /**
     * Top 5 músicas mais tocadas
     */
    public function top5(): JsonResponse
    {
        $musicas = Musica::top5()->get();

        return response()->json([
            'success' => true,
            'data' => $musicas
        ]);
    }

    /**
     * Demais músicas (6ª em diante)
     */
    public function demais(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        
        $musicas = Musica::demais()->paginate($perPage);

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'url_youtube' => 'required|url',
            ]);

            $videoId = $this->youtubeService::extrairVideoId($request->url_youtube);
            if (!$videoId) {
                throw ValidationException::withMessages([
                    'url_youtube' => 'URL do YouTube inválida'
                ]);
            }

            // Verifica se já existe
            if (Musica::where('youtube_id', $videoId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta música já está cadastrada'
                ], 409);
            }

            // Busca informações do vídeo
            $videoInfo = $this->youtubeService->getVideoInfo($videoId);

            // Cria a música
            $musica = Musica::create($videoInfo);

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

    /**
     * Display the specified resource.
     */
    public function show(Musica $musica): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $musica
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Musica $musica): JsonResponse
    {
        try {
            $request->validate([
                'titulo' => 'sometimes|string|max:255',
                'visualizacoes' => 'sometimes|integer|min:0',
                'status' => 'sometimes|in:ativa,inativa',
                'url_youtube' => 'sometimes|url',
            ]);

            $data = $request->only(['titulo', 'visualizacoes', 'status']);

            // Se foi fornecida nova URL, atualiza youtube_id e thumb
            if ($request->has('url_youtube')) {
                $videoId = $this->youtubeService::extrairVideoId($request->url_youtube);
                if (!$videoId) {
                    throw ValidationException::withMessages([
                        'url_youtube' => 'URL do YouTube inválida'
                    ]);
                }

                // Verifica se outro registro já usa este youtube_id
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

            $musica->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Música atualizada com sucesso',
                'data' => $musica->fresh()
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Musica $musica): JsonResponse
    {
        try {
            $musica->delete();

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

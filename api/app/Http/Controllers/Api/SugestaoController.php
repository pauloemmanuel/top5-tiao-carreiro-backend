<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sugestao;
use App\Services\SugestaoService;
use App\Http\Requests\Sugestao\StoreSugestaoRequest;
use App\Http\Requests\Sugestao\ProcessarSugestaoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SugestaoController extends Controller
{
    private SugestaoService $sugestaoService;

    public function __construct(SugestaoService $sugestaoService)
    {
        $this->sugestaoService = $sugestaoService;
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $status = $request->get('status');

            $sugestoes = $this->sugestaoService->index($perPage, $status);

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
        } catch (\Exception $e) {
            Log::error('Erro ao listar sugestões: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function store(StoreSugestaoRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $sugestao = $this->sugestaoService->store($validated, $request->ip());

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
        try {
            $sugestao = $this->sugestaoService->show($sugestao->id);

            if (!$sugestao) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sugestão não encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $sugestao
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar sugestão: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function aprovar(ProcessarSugestaoRequest $request, Sugestao $sugestao): JsonResponse
    {
        try {
            $validated = $request->validated();

            $result = $this->sugestaoService->aprovar($sugestao, $request->user(), $request->observacoes);

            return response()->json([
                'success' => true,
                'message' => 'Sugestão aprovada e música adicionada com sucesso',
                'data' => $result
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
            $validated = $request->validated();

            $this->sugestaoService->rejeitar($sugestao, $request->user(), $request->observacoes);

            return response()->json([
                'success' => true,
                'message' => 'Sugestão rejeitada com sucesso'
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
            $this->sugestaoService->delete($sugestao);

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

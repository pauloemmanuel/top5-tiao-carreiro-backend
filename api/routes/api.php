<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MusicaController;
use App\Http\Controllers\Api\SugestaoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check
Route::get('/ping', function () {
    return response()->json([
        'message' => 'Top 5 Tião Carreiro API está funcionando!',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Rotas de autenticação
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    });
});

// Rotas de músicas
Route::prefix('musicas')->group(function () {
    // Rotas públicas
    Route::get('/', [MusicaController::class, 'index']);
    Route::get('/top5', [MusicaController::class, 'top5']);
    Route::get('/demais', [MusicaController::class, 'demais']);
    Route::get('/{musica}', [MusicaController::class, 'show']);
    
    // Rotas protegidas (requer autenticação)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [MusicaController::class, 'store']);
        Route::put('/{musica}', [MusicaController::class, 'update']);
        Route::delete('/{musica}', [MusicaController::class, 'destroy']);
    });
});

// Rotas de sugestões
Route::prefix('sugestoes')->group(function () {
    // Rota pública para enviar sugestões
    Route::post('/', [SugestaoController::class, 'store']);
    
    // Rotas protegidas (requer autenticação)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [SugestaoController::class, 'index']);
        Route::get('/{sugestao}', [SugestaoController::class, 'show']);
        Route::post('/{sugestao}/aprovar', [SugestaoController::class, 'aprovar']);
        Route::post('/{sugestao}/rejeitar', [SugestaoController::class, 'rejeitar']);
        Route::delete('/{sugestao}', [SugestaoController::class, 'destroy']);
    });
});

// Fallback para rotas não encontradas
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint não encontrado'
    ], 404);
});

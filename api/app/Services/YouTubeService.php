<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class YouTubeService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
    }

    /**
     * Extrai informações do vídeo do YouTube
     */
    public function getVideoInfo(string $videoId): array
    {
        try {
            $url = "https://www.youtube.com/watch?v=" . $videoId;
            
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();

            // Extrai título
            $titulo = $this->extrairTitulo($html);
            if (!$titulo) {
                throw new \Exception("Não foi possível encontrar o título do vídeo");
            }

            // Extrai visualizações
            $visualizacoes = $this->extrairVisualizacoes($html);

            // Gera thumbnail
            $thumb = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";

            return [
                'titulo' => $titulo,
                'visualizacoes' => $visualizacoes,
                'youtube_id' => $videoId,
                'thumb' => $thumb
            ];

        } catch (RequestException $e) {
            Log::error('Erro ao acessar YouTube: ' . $e->getMessage());
            throw new \Exception("Erro ao acessar o YouTube");
        } catch (\Exception $e) {
            Log::error('Erro ao extrair informações do vídeo: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extrai o título do HTML
     */
    private function extrairTitulo(string $html): ?string
    {
        // Padrão para título
        if (preg_match('/<title>(.+?) - YouTube<\/title>/', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Padrão alternativo
        if (preg_match('/"title":"([^"]+)"/', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    /**
     * Extrai o número de visualizações do HTML
     */
    private function extrairVisualizacoes(string $html): int
    {
        // Padrão para visualizações
        if (preg_match('/"viewCount":\s*"(\d+)"/', $html, $matches)) {
            return (int)$matches[1];
        }

        // Padrão alternativo
        if (preg_match('/\"viewCount\"\s*:\s*{.*?\"simpleText\"\s*:\s*\"([\d,\.]+)\"/', $html, $matches)) {
            return (int)str_replace(['.', ','], '', $matches[1]);
        }

        return 0;
    }

    /**
     * Extrai o ID do vídeo de uma URL do YouTube
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
     * Valida se uma URL é do YouTube
     */
    public static function isYouTubeUrl(string $url): bool
    {
        return (bool)self::extrairVideoId($url);
    }
}

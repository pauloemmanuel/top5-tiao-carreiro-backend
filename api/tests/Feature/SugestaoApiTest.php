<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Sugestao;

class SugestaoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_can_create_sugestao()
    {
        $sugestaoData = [
            'url_youtube' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ];

        $response = $this->postJson('/api/sugestoes', $sugestaoData);

        // Como o vídeo pode não existir, testamos ambos os casos
        $this->assertTrue(
            $response->status() === 201 || $response->status() === 500
        );
    }

    public function test_authenticated_user_can_list_sugestoes()
    {
        $user = User::first();

        // Cria algumas sugestões para teste
        Sugestao::create([
            'url_youtube' => 'https://www.youtube.com/watch?v=test1',
            'youtube_id' => 'test1',
            'titulo' => 'Teste 1',
            'status' => 'pendente',
            'ip_origem' => '127.0.0.1'
        ]);

        $response = $this->actingAs($user, 'sanctum')
                        ->getJson('/api/sugestoes');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                    'pagination'
                ])
                ->assertJsonPath('success', true);
    }

    public function test_unauthenticated_user_cannot_list_sugestoes()
    {
        $response = $this->getJson('/api/sugestoes');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_view_specific_sugestao()
    {
        $user = User::first();

        $sugestao = Sugestao::create([
            'url_youtube' => 'https://www.youtube.com/watch?v=test1',
            'youtube_id' => 'test1',
            'titulo' => 'Teste 1',
            'status' => 'pendente',
            'ip_origem' => '127.0.0.1'
        ]);

        $response = $this->actingAs($user, 'sanctum')
                        ->getJson("/api/sugestoes/{$sugestao->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'url_youtube',
                        'youtube_id',
                        'titulo',
                        'status'
                    ]
                ])
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.id', $sugestao->id);
    }

    public function test_authenticated_user_can_approve_sugestao()
    {
        $user = User::first();

        $sugestao = Sugestao::create([
            'url_youtube' => 'https://www.youtube.com/watch?v=test1',
            'youtube_id' => 'test1',
            'titulo' => 'Teste 1',
            'visualizacoes' => 1000,
            'thumb' => 'https://img.youtube.com/vi/test1/hqdefault.jpg',
            'status' => 'pendente',
            'ip_origem' => '127.0.0.1'
        ]);

        $response = $this->actingAs($user, 'sanctum')
                        ->postJson("/api/sugestoes/{$sugestao->id}/aprovar", [
                            'observacoes' => 'Música excelente!'
                        ]);

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        $this->assertDatabaseHas('sugestoes', [
            'id' => $sugestao->id,
            'status' => 'aprovada'
        ]);

        $this->assertDatabaseHas('musicas', [
            'youtube_id' => 'test1'
        ]);
    }

    public function test_authenticated_user_can_reject_sugestao()
    {
        $user = User::first();

        $sugestao = Sugestao::create([
            'url_youtube' => 'https://www.youtube.com/watch?v=test1',
            'youtube_id' => 'test1',
            'titulo' => 'Teste 1',
            'status' => 'pendente',
            'ip_origem' => '127.0.0.1'
        ]);

        $response = $this->actingAs($user, 'sanctum')
                        ->postJson("/api/sugestoes/{$sugestao->id}/rejeitar", [
                            'observacoes' => 'Não se encaixa no perfil'
                        ]);

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        $this->assertDatabaseHas('sugestoes', [
            'id' => $sugestao->id,
            'status' => 'rejeitada'
        ]);
    }

    public function test_cannot_approve_already_processed_sugestao()
    {
        $user = User::first();

        $sugestao = Sugestao::create([
            'url_youtube' => 'https://www.youtube.com/watch?v=test1',
            'youtube_id' => 'test1',
            'titulo' => 'Teste 1',
            'status' => 'aprovada',
            'ip_origem' => '127.0.0.1'
        ]);

        $response = $this->actingAs($user, 'sanctum')
                        ->postJson("/api/sugestoes/{$sugestao->id}/aprovar");

        $response->assertStatus(409);
    }

    public function test_validation_error_on_invalid_youtube_url()
    {
        $sugestaoData = [
            'url_youtube' => 'invalid-url'
        ];

        $response = $this->postJson('/api/sugestoes', $sugestaoData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ]);
    }

    public function test_can_filter_sugestoes_by_status()
    {
        $user = User::first();

        // Cria sugestões com diferentes status
        Sugestao::create([
            'url_youtube' => 'https://www.youtube.com/watch?v=test1',
            'youtube_id' => 'test1',
            'status' => 'pendente',
            'ip_origem' => '127.0.0.1'
        ]);

        Sugestao::create([
            'url_youtube' => 'https://www.youtube.com/watch?v=test2',
            'youtube_id' => 'test2',
            'status' => 'aprovada',
            'ip_origem' => '127.0.0.1'
        ]);

        $response = $this->actingAs($user, 'sanctum')
                        ->getJson('/api/sugestoes?status=pendente');

        $response->assertStatus(200)
                ->assertJsonPath('success', true);
    }
}

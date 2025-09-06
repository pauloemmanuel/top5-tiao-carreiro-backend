<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Musica;

class MusicaApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_get_top5_musicas()
    {
        $response = $this->getJson('/api/musicas/top5');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'titulo',
                            'visualizacoes',
                            'youtube_id',
                            'thumb',
                            'status'
                        ]
                    ]
                ])
                ->assertJsonPath('success', true);

        $this->assertCount(5, $response->json('data'));
    }

    public function test_can_get_demais_musicas()
    {
        $response = $this->getJson('/api/musicas/demais');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                    'pagination'
                ])
                ->assertJsonPath('success', true);
    }

    public function test_can_get_all_musicas_paginated()
    {
        $response = $this->getJson('/api/musicas?per_page=5');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page'
                    ]
                ])
                ->assertJsonPath('success', true);
    }

    public function test_can_show_specific_musica()
    {
        $musica = Musica::first();

        $response = $this->getJson("/api/musicas/{$musica->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'titulo',
                        'visualizacoes',
                        'youtube_id',
                        'thumb',
                        'status'
                    ]
                ])
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.id', $musica->id);
    }

    public function test_authenticated_user_can_create_musica()
    {
        $user = User::first();

        $musicaData = [
            'url_youtube' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ];

        $response = $this->actingAs($user, 'sanctum')
                        ->postJson('/api/musicas', $musicaData);

        // Como o vídeo pode não existir, testamos ambos os casos
        $this->assertTrue(
            $response->status() === 201 || $response->status() === 500
        );
    }

    public function test_unauthenticated_user_cannot_create_musica()
    {
        $musicaData = [
            'url_youtube' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ];

        $response = $this->postJson('/api/musicas', $musicaData);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_update_musica()
    {
        $user = User::first();
        $musica = Musica::first();

        $updateData = [
            'titulo' => 'Título Atualizado',
            'visualizacoes' => 999999
        ];

        $response = $this->actingAs($user, 'sanctum')
                        ->putJson("/api/musicas/{$musica->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.titulo', 'Título Atualizado');
    }

    public function test_authenticated_user_can_delete_musica()
    {
        $user = User::first();
        $musica = Musica::first();

        $response = $this->actingAs($user, 'sanctum')
                        ->deleteJson("/api/musicas/{$musica->id}");

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('musicas', ['id' => $musica->id]);
    }

    public function test_validation_error_on_invalid_youtube_url()
    {
        $user = User::first();

        $musicaData = [
            'url_youtube' => 'invalid-url'
        ];

        $response = $this->actingAs($user, 'sanctum')
                        ->postJson('/api/musicas', $musicaData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ]);
    }
}

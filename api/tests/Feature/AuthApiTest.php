<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ],
                        'token',
                        'token_type'
                    ]
                ])
                ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com'
        ]);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::first();

        $loginData = [
            'email' => $user->email,
            'password' => 'password123', // senha padrão do seeder
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user',
                        'token',
                        'token_type'
                    ]
                ])
                ->assertJsonPath('success', true);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $loginData = [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ])
                ->assertJsonPath('success', false);
    }

    public function test_authenticated_user_can_get_profile()
    {
        $user = User::first();

        $response = $this->actingAs($user, 'sanctum')
                        ->getJson('/api/auth/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'name',
                        'email',
                    ]
                ])
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.id', $user->id);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::first();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
                        'Authorization' => 'Bearer ' . $token
                    ])
                    ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        // Verifica se o token foi revogado
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    public function test_register_validation_errors()
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123', // muito curta
        ];

        $response = $this->postJson('/api/auth/register', $invalidData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => [
                        'name',
                        'email',
                        'password'
                    ]
                ]);
    }

    public function test_cannot_register_with_existing_email()
    {
        $existingUser = User::first();

        $userData = [
            'name' => 'Novo Usuário',
            'email' => $existingUser->email, // email já existe
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => [
                        'email'
                    ]
                ]);
    }
}

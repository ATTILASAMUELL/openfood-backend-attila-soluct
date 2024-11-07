<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_register_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Usuário criado com sucesso'
                 ]);
    }

    public function test_login_user()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Login realizado com sucesso.'
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'access_token',
                         'token_type',
                     ]
                 ]);
    }

    public function test_logout_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Logout realizado com sucesso.'
                 ]);
    }

    public function test_get_user_profile()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->getJson('/api/profile');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Perfil carregado com sucesso.'
                 ])
                 ->assertJsonStructure([
                     'data' => ['id', 'name', 'email', 'created_at', 'updated_at']
                 ]);
    }

    public function test_refresh_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->postJson('/api/refresh');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Token atualizado com sucesso.'
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'access_token',
                         'token_type',
                     ]
                 ]);
    }
}

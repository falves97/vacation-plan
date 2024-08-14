<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cria um usuário de teste
     * @return TestResponse
     */
    public function registerTestUser(): TestResponse
    {
        return $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => 'password',
        ]);
    }

    /**
     * Teste de sucesso no registro de usuário
     *
     * @return void
     */
    public function test_register_success(): void
    {
        $response = $this->registerTestUser();

        $response->assertStatus(201);
    }

    /**
     * Teste de falha no registro de usuário
     *
     * @return void
     */
    public function test_register_fail()
    {
        /** Primeiro, tenta registrar um usuário com e-mail inválido */
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testemail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        /** Depois, tenta registrar um usuário com e-mail e senha inválidos */
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testemail.com',
            'password' => 'pass',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Teste de sucesso no login de usuário
     *
     * @return void
     */
    public function test_login_success(): void
    {
        /** Primeiro, registra um usuário */
        $this->registerTestUser();

        /** Depois, faz o login */
        $response = $this->postJson('/api/login', [
            'email' => 'test@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'token_type', 'expires_at']);
    }

    /**
     * Teste de falha no login de usuário
     *
     * @return void
     */
    public function test_login_fail(): void
    {
        /** Primeiro, registra um usuário */
        $response = $this->registerTestUser();

        $response->assertStatus(201);

        /** Depois, tenta fazer o login com e-mail inválido */
        $response = $this->postJson('/api/login', [
            'email' => 'testemail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);

        /** Depois, tenta fazer o login com senha inválida */
        $response = $this->postJson('/api/login', [
            'email' => 'test@email.com',
            'password' => 'pass',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Teste de sucesso no logout de usuário
     *
     * @return void
     */
    public function test_logout_success(): void

    {
        /** Primeiro, registra um usuário */
        $response = $this->registerTestUser();

        $response->assertStatus(201);

        /** Depois, faz o login */
        $response = $this->postJson('/api/login', [
            'email' => 'test@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $token = $response->json('token');

        /** Depois, faz o logout */
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->getJson('/api/logout');

        $response->assertStatus(204);
    }

    /**
     * Teste de falha no logout de usuário
     *
     * @return void
     */
    public function test_logout_fail(): void
    {
        $response = $this->getJson('/api/logout');

        $response->assertStatus(401);

        $response = $this->withHeader('Authorization', 'Bearer invalid_token')->getJson('/api/logout');

        $response->assertStatus(401);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration
     *
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
     * Test user registration success
     *
     * @return void
     */
    public function test_register_success(): void
    {
        $response = $this->registerTestUser();

        $response->assertStatus(201);
    }

    /**
     * Test user registration failure
     *
     * @return void
     */
    public function test_register_fail()
    {
        /**
         * First, try to register a user with an invalid email.
         */
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testemail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        /**
         * After that, try to register a user with an invalid email and password.
         */
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testemail.com',
            'password' => 'pass',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test user log in success
     *
     * @return void
     */
    public function test_login_success(): void
    {
        /**
         * First, register a user.
         */
        $this->registerTestUser();

        /**
         * After that, log in.
         */
        $response = $this->postJson('/api/login', [
            'email' => 'test@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'token_type', 'expires_at']);
    }

    /**
     * Test user log in failure
     *
     * @return void
     */
    public function test_login_fail(): void
    {
        /**
         * First, register a user.
         */
        $response = $this->registerTestUser();

        $response->assertStatus(201);

        /**
         * After that, try to log in with an invalid email.
         */
        $response = $this->postJson('/api/login', [
            'email' => 'testemail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);

        /**
         * After that, try to log in with an invalid password.
         */
        $response = $this->postJson('/api/login', [
            'email' => 'test@email.com',
            'password' => 'pass',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test user log out success
     *
     * @return void
     */
    public function test_logout_success(): void

    {
        /**
         * First, register a user.
         */
        $response = $this->registerTestUser();

        $response->assertStatus(201);

        /**
         * After that, log in.
         */
        $response = $this->postJson('/api/login', [
            'email' => 'test@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $token = $response->json('token');

        /**
         * After that, log out.
         */
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->getJson('/api/logout');

        $response->assertStatus(204);
    }

    /**
     * Test user log out failure
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

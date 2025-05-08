<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_user_login(){
        $user = User::factory()->create();

        if (!$user) {
            $user = User::factory()->create();
        }

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token'
        ]);
    }

    public function test_failed_user_login(){
        $user = User::first();

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJson(["message" => "Insira um email ou senha válidos."]);
    }

    public function test_user_logout(){
        $user = User::first();

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $user['token'];

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/logout', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'token expirado']);
    }
}
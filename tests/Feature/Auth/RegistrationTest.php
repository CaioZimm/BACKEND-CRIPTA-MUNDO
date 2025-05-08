<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_register_user(){
        $user = [
            'name' => 'teste',
            'email' => "teste@teste.com",
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        if (User::where('email', $user['email'])->exists()) {
            $user['email'] = fake()->unique()->safeEmail();
        }

        $response = $this->postJson('/register',$user);
        $response->dump();

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Registrado com Sucesso!']);

        $this->assertDatabaseHas('users', [
            'email' => $user['email']
        ]);
    }
    
    public function test_register_email_already_used(){
        $user = [
            'name' => 'teste',
            'email' => 'teste@teste.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/register',$user);

        $response->assertStatus(422);
        $response->assertJson([ "message" => "The email has already been taken."]);

        $this->assertDatabaseHas('users', [
            'email' => 'teste@teste.com'
        ]);
    }

    public function test_register_field_email_required(){
        $user = [
            'name' => 'teste',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/register',$user);

        $response->assertStatus(422);
        $response->assertJson([ "message" => "The email field is required."]);
    }
}
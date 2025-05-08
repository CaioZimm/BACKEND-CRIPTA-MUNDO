<?php

namespace Tests\Feature\User;

use Faker\Factory as Faker;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase{

    public function test_user_unauthenticated(){

        $this->json('get', 'api/user')->assertStatus(401)
                ->assertJson(["message" => "Unauthenticated."]);
    }

    public function test_admin_can_access_users_index() {
        $user = User::factory()->create([
            'usertype' => 'admin'
        ]);
    
        $token = $user->createToken('token')->plainTextToken;
    
        $response = $this->withHeader('Authorization', 'Bearer ' . $token);
    
        $response->json('get', 'api/user')->assertStatus(200);
    }

    public function test_user_cannot_acess_user_index(){
        $user = User::factory()->create([
            'usertype' => 'user'
        ]);
        
        $token = $user->createToken('token')->plainTextToken;
    
        $response = $this->withHeader('Authorization', 'Bearer ' . $token);

        $response->getJson("api/user")->assertStatus(401)
                ->assertJson(['message' => 'Unauthorized']);
    }

    public function test_user_show_profile() {
        $user = User::first();

        $token = $user->createToken('token')->plainTextToken;
    
        $response = $this->withHeader('Authorization', 'Bearer ' . $token);

        $response->getJson("api/profile")->assertStatus(200);
    }

    public function test_user_show() {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create();
        }

        $token = $user->createToken('token')->plainTextToken;
    
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->getJson("api/user/{$user->id}");
                    
        $response->assertStatus(200);
    }

    public function test_user_show_not_found() {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create();
        }

        $token = $user->createToken('token')->plainTextToken;

        $idNonExisted = 9123851723;
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->getJson("api/user/{$idNonExisted}");
                    
        $response->assertStatus(404)->assertJson(["erro" => "Nada encontrado"]);
    }

    public function test_user_update() {
        $user = User::factory()->create();

        $token = $user->createToken('token')->plainTextToken;
    
        $faker = Faker::create();
        $randomEmail = $faker->unique()->safeEmail;

        $updateData = [
            'name' => 'Nome Atualizado',
            'email' => $randomEmail,
            '_method' => 'PUT'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->putJson("/api/user", $updateData);

        $response->assertStatus(200)->assertJson([
            'message' => 'Atualizado com sucesso'
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Atualizado',
            'email' => $randomEmail
        ]);
    }

    public function test_user_update_new_password() {
        $user = User::factory()->create();

        $token = $user->createToken('token')->plainTextToken;

        $updatedPassword = [
            'password_current' => 'password',
            'new_password' => '12345678',
            'new_password_confirmation' => '12345678'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->putJson("/api/newpassword", $updatedPassword);

        $response->assertStatus(200)->assertJson(['message' => 'Senha atualizada com sucesso!']);
    }

    public function test_user_update_new_password_incorrect() {
        $user = User::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $updatedPassword = [
            'password_current' => 'wrong-password',
            'new_password' => '12345678',
            'new_password_confirmation' => '12345678'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->putJson("/api/newpassword", $updatedPassword);

        $response->assertStatus(404)->assertJson(['message' => 'A senha atual está incorreta']);
    }

    public function test_user_update_new_password_same_passwords() {
        $user = User::factory()->create([
            'password' => Hash::make('password')
        ]);

        $token = $user->createToken('token')->plainTextToken;

        $updatedPassword = [
            'password_current' => 'password',
            'new_password' => 'password',
            'new_password_confirmation' => 'password'
        ];

        $response = $this->putJson("/api/newpassword", $updatedPassword, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(409)->assertJson(['message' => 'A nova senha deve ser diferente da anterior']);
    }

    public function test_user_delete() {
        $user = User::factory()->create();

        $token = $user->createToken('token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->deleteJson("/api/user");

        $response->assertStatus(200)->assertJson(['message' => 'Seu usuário foi excluído com sucesso!']);
    }
}
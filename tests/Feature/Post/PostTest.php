<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function test_post_index(){

        if(!Post::first()){
            $this->getJson('api/post')->assertStatus(204);
        } else{
            $this->getJson('api/post')->assertStatus(200)
                    ->assertJsonStructure([
                        'data'
                    ]);
        }
    }
    
    public function test_admin_create_post(){
        $admin = User::where('usertype', 'admin')->first();

        $token = $admin->createToken('token')->plainTextToken;

        $image = UploadedFile::fake()->image('post_image.jpg');
        $post = [
            'user_id' => $admin->id,
            'title' => fake()->sentence(3),
            'caption' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'image' => $image
        ];

        $response = $this->postJson('/api/post', $post, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'user_id' => $post['user_id'],
                'title' => $post['title'],
                'caption' => $post['caption'],
                'description' => $post['description']
            ]
        ]);
    }

    public function test_cannot_create_post(){
        $user = User::where('usertype', 'user')->first();

        $token = $user->createToken('token')->plainTextToken;

        $image = UploadedFile::fake()->image('post_image.jpg');
        $post = [
            'user_id' => $user->id,
            'title' => fake()->sentence(3),
            'caption' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'image' => $image
        ];

        $response = $this->postJson('/api/post', [$post], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Unauthorized']);
    }

    public function test_post_show(){
        $user = User::first();
        $post = Post::first();

        $token = $user->createToken('token')->plainTextToken;

        $response = $this->getJson("/api/post/{$post->id}", ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(200)->assertJson([ 
            'data' => [
                'id' => $post['id'],
                'user_id' => $post['user_id'],
                'title' => $post['title'],
                'caption' => $post['caption'],
                'description' => $post['description']
            ] 
        ]);
    }

    public function test_post_show_unauthenticated(){

        $post = Post::first();

        $response = $this->getJson("/api/post/{$post->id}");

        $response->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_post_show_not_found() {
        $user = User::first();

        $token = $user->createToken('token')->plainTextToken;

        $idNonExisted = 9123851723;
        $response = $this->getJson("/api/post/{$idNonExisted}", ['Authorization' => 'Bearer ' . $token]);
                    
        $response->assertStatus(404)->assertJson(["erro" => "Nada encontrado"]);
    }

    public function test_post_update() {
        $admin = User::where('usertype', 'admin')->first();
        $post = Post::factory()->create();

        $token = $admin->createToken('token')->plainTextToken;

        $image = UploadedFile::fake()->image('post_image.jpg');
        $updatedPost = [
            'title' => 'Post Atualizado',
            'caption' => 'SubTitulo atualizado',
            'description' => 'Descrição 2',
            'image' => $image,
            '_method' => 'PUT'
        ];

        $response = $this->putJson("/api/post/{$post->id}", $updatedPost, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJson([
            'message' => 'Atualizado com sucesso!',
            'data' => [
                'title' => 'Post Atualizado',
                'caption' => 'SubTitulo atualizado',
                'description' => 'Descrição 2',
            ]
        ]);
    }

    public function test_post_delete(){
        $admin = User::where('usertype', 'admin')->first();
        $post = Post::inRandomOrder()->first();

        if (!$post) {
            $post = Post::factory()->create();
        }

        $token = $admin->createToken('token')->plainTextToken;

        $response = $this->deleteJson("/api/post/{$post->id}", [], [ 'Authorization' => 'Bearer ' . $token ]);

        $response->assertStatus(204);
    }
}
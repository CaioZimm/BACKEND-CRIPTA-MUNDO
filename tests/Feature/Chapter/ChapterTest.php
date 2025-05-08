<?php

namespace Tests\Feature\Chapter;

use App\Models\Chapter;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ChapterTest extends TestCase
{
    public function test_admin_chapter_create(){
        $admin = User::where('usertype', 'admin')->first();
        $post = Post::first();

        $token = $admin->createToken('token')->plainTextToken;

        $image = UploadedFile::fake()->image('chapter_image.jpg');
        $chapter = [
            'post_id' =>  $post->id,
            'title' => fake()->sentence(2),
            'content' => fake()->paragraph(),
            'image' => $image
        ];

        $response = $this->postJson('/api/chapter', $chapter, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'post_id' => $chapter['post_id'],
                'title' => $chapter['title'],
                'content' => $chapter['content']
            ]
        ]);
    }

    public function test_user_cannot_create_chapter(){
        $user = User::where('usertype', 'user')->first();
        $post = Post::first();

        $token = $user->createToken('token')->plainTextToken;

        $chapter = [
            'post_id' =>  $post->id,
            'title' => fake()->sentence(2),
            'content' => fake()->paragraph()
        ];

        $response = $this->postJson('/api/post', [$chapter], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Unauthorized']);
    }

    public function test_chapter_index(){
        $user = User::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $response = $this->getJson("/api/chapter", ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(200)->assertJson([ 
            'data' => []
        ]);
    }

    public function test_chapter_show(){
        $user = User::inRandomOrder()->first();
        $chapter = Chapter::first();

        $token = $user->createToken('token')->plainTextToken;

        $response = $this->getJson("/api/chapter/{$chapter->id}", ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(200)->assertJson([ 
            'data' => [
                'post_id' => $chapter['post_id'],
                'title' => $chapter['title'],
                'content' => $chapter['content']
            ]
        ]);
    }

    public function test_chapter_show_not_found(){
        $user = User::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $idNonExisted = 9123851723;
        $response = $this->getJson("/api/chapter/{$idNonExisted}", ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(404)->assertJson(['message' => 'Nada encontrado']);
    }

    public function test_chapter_update(){
        $admin = User::where('usertype', 'admin')->first();
        $chapter = Chapter::factory()->create();

        $token = $admin->createToken('token')->plainTextToken;

        $image = UploadedFile::fake()->image('chapter_image.jpg');
        $updatedChapter = [
            'title' => 'Capitulo Atualizado',
            'content' => 'Ta atualizado aqui',
            'image' => $image,
            '_method' => 'PUT'
        ];

        $response = $this->putJson("/api/chapter/{$chapter->id}", $updatedChapter, 
                                ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(200)->assertJson([
            'message' => 'Atualizado com sucesso!',
            'data' => [
                'title' => 'Capitulo Atualizado',
                'content' => 'Ta atualizado aqui'
            ]
        ]);
    }

    public function test_chapter_cannot_update(){
        $user = User::where('usertype', 'user')->first();
        $chapter = Chapter::first();

        $token = $user->createToken('token')->plainTextToken;

        $updatedChapter = [
            'title' => 'Capitulo Atualizado',
            'content' => 'Ta atualizado aqui',
            '_method' => 'PUT'
        ];

        $response = $this->putJson("/api/chapter/{$chapter->id}", $updatedChapter, 
                                ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(401)->assertJson(['message' => 'Unauthorized']);
    }

    public function test_chapter_delete(){
        $admin = User::where('usertype', 'admin')->first();
        $chapter = Chapter::inRandomOrder()->first();

        if (!$chapter) {
            $chapter = Chapter::factory()->create();
        }

        $token = $admin->createToken('token')->plainTextToken;

        $response = $this->deleteJson("/api/chapter/{$chapter->id}", [], [ 'Authorization' => 'Bearer ' . $token ]);

        $response->assertStatus(204);
    }
}

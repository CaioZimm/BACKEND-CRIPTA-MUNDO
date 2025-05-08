<?php

namespace Tests\Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function test_comment_create(){
        $user = User::inRandomOrder()->first();
        $post = Post::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $comment = [
            'post_id' =>  $post->id,
            'description' => fake()->sentence(4)
        ];

        $response = $this->postJson('/api/comment', $comment, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'post_id' => $comment['post_id'],
                'description' => $comment['description']
            ]
        ]);
    }

    public function test_comment_create_unauthenticated(){
        $post = Post::first();
        
        $comment = [
            'post_id' =>  $post->id,
            'description' => fake()->sentence(4)
        ];

        $response = $this->postJson('/api/comment', [$comment]);

        $response->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_comment_index(){
        $user = User::inRandomOrder()->first();
        $token = $user->createToken('token')->plainTextToken;

        if(!Comment::first()){
            $this->getJson('api/comment',['Authorization' => 'Bearer ' . $token])->assertStatus(204);
        } else{
            $this->getJson('api/comment', ['Authorization' => 'Bearer ' . $token])->assertStatus(200)
                    ->assertJsonStructure([
                        'data'
                    ]);
        }
    }

    public function test_comment_show() {
        $user = User::first();
        $comment = Comment::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $response = $this->getJson("/api/comment/{$comment->id}", ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(200)->assertJson([ 
            'data' => [
                'id' => $comment['id'],
                'user_id' => $comment['user_id'],
                'post_id' => $comment['post_id'],
                'description' => $comment['description']
            ]
        ]);
    }

    public function test_comment_show_not_found() {
        $user = User::first();

        $token = $user->createToken('token')->plainTextToken;

        $idNonExisted = 9123851723;
        $response = $this->getJson("/api/comment/{$idNonExisted}", ['Authorization' => 'Bearer ' . $token]);
                    
        $response->assertStatus(404)->assertJson(["erro" => "Nada encontrado"]);
    }

    public function test_comment_update(){
        $user = User::inRandomOrder()->first();
        $comment = Comment::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $updatedComment = [
            'description' => 'Atualizado',
            '_method' => 'PUT'
        ];

        if($comment->user_id === $user->id){
            $response = $this->putJson("/api/comment/{$comment->id}", $updatedComment, [
                'Authorization' => 'Bearer ' . $token
            ]);

            $response->assertStatus(200)->assertJson([
                'message' => 'Atualizado com sucesso',
                'data' => [
                    'description' => 'Atualizado'
                ]
            ]);
        } else {
            $this->putJson("/api/comment/{$comment->id}", $updatedComment, ['Authorization' => 'Bearer ' . $token])
                 ->assertStatus(403)
                 ->assertJson([
                        'erro' => 'Você não possui permissão para editar esse comentário'
                    ]);
        }
    }

    public function test_comment_delete(){
        $user = User::inRandomOrder()->first();
        $comment = Comment::where('user_id', $user->id)->first();

        if (!$comment) {
            $comment = Comment::factory()->create(['user_id' => $user->id]);
        }

        $token = $user->createToken('token')->plainTextToken;

        $response = $this->deleteJson("api/comment/{$comment->id}", [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJson(['message' => 'Comentário excluído com sucesso!']);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_comment_delete_forbidden(){
        $user = User::inRandomOrder()->first();
        $comment = Comment::where('user_id', '!=',$user->id)->first();

        if (!$comment) {
            $anotherUser = User::where('id', '!=', $user->id)->first();
            $comment = Comment::factory()->create(['user_id' => $anotherUser]);
        }

        $token = $user->createToken('token')->plainTextToken;

        $response = $this->deleteJson("api/comment/{$comment->id}", [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(403)->assertJson([
            'erro' => 'Você não possui permissão para deletar esse comentário'
        ]);
    }
}
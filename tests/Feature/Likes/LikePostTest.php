<?php

namespace Tests\Feature\Likes;

use App\Models\LikePost;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class LikePostTest extends TestCase
{
    public function test_like_post() {
        $user = User::inRandomOrder()->first();
        $post = Post::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $likePost = [
            'post_id' =>  $post->id,
        ];

        $response = $this->postJson( "/api/like_post", $likePost, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJson([
            'message' => 'Você curtiu essa postagem',
            'data' => []
        ]);
    }

    public function test_dislike_post() {
        $user = User::inRandomOrder()->first();
        $post = Post::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $like = LikePost::where('user_id', $user->id)
                        ->where('post_id', $post->id)
                        ->first();

        $likePost = [
            'post_id' =>  $post->id,
        ];

        if (!$like){
            $response = $this->postJson( "/api/like_post", $likePost, [
                'Authorization' => 'Bearer ' . $token
            ]);
        }

        $response = $this->postJson( "/api/like_post", $likePost, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJson([
            'message' => 'Seu like foi removido'
        ]);
    }
}
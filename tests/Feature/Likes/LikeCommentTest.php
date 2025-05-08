<?php

namespace Tests\Feature\Likes;

use App\Models\Comment;
use App\Models\LikeComment;
use App\Models\User;
use Tests\TestCase;

class LikeCommentTest extends TestCase
{
    public function test_like_comment() {
        $user = User::inRandomOrder()->first();
        $comment = Comment::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $likeComment = [
            'comment_id' =>  $comment->id,
        ];

        $response = $this->postJson( "/api/like_comment", $likeComment, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJson([
            'message' => 'Você curtiu esse comentário', 
            'liked' => true, 
            'data' => []
        ]);
    }

    public function test_dislike_post() {
        $user = User::inRandomOrder()->first();
        $comment = Comment::inRandomOrder()->first();

        $token = $user->createToken('token')->plainTextToken;

        $like = LikeComment::where('user_id', $user->id)
                            ->where('comment_id', $comment->id)
                            ->first();

        $likeComment = [
            'comment_id' =>  $comment->id,
        ];

        if(!$like){
            $response = $this->postJson( "/api/like_comment", $likeComment, [
                'Authorization' => 'Bearer ' . $token
            ]);
        }

        $response = $this->postJson( "/api/like_comment", $likeComment, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJson([
            'message' => 'Seu like foi removido do comentário', 
            'liked' => false,
            'data' => []
        ]);
    }
}
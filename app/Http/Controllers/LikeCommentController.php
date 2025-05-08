<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeCommentRequest;
use App\Models\Comment;
use App\Models\LikeComment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LikeCommentController extends Controller
{
    public function store(LikeCommentRequest $request){

        $user = $request->user();

        $like = LikeComment::where('user_id', $user->id)
                            ->where('comment_id', $request->comment_id)
                            ->first();

        if($like) {
            $comment = Comment::find($request->comment_id);
            $like->delete();
            return response()->json([ 'message' => 'Seu like foi removido do comentário', 'liked' => false,
                                      'data' => $comment], Response::HTTP_OK);
        } else {

            $like = new LikeComment();
            $like->user_id = $user->id;
            $like->comment_id = $request->comment_id;

            if($like->save()) {
                $comment = Comment::find($request->comment_id);
                $comment->like = $user->likeComments()->where('comment_id', $comment->id)->exists();
                $comment->user = $user;
                return response()->json([ 'message' => 'Você curtiu esse comentário', 'liked' => true, 
                                          'data' => $comment], Response::HTTP_OK);
            } else {
                return response()->json([ 'message' => 'Ocorreu algum erro'], Response::HTTP_CONFLICT);
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikePostRequest;
use App\Models\LikePost;
use Symfony\Component\HttpFoundation\Response;

class LikePostController extends Controller
{
    public function store(LikePostRequest $request)
    {

        $user = $request->user();

        $like = LikePost::where('user_id', $user->id)
                        ->where('post_id', $request->post_id)
                        ->first();

        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Seu like foi removido'], Response::HTTP_OK);
        } else {

            $like = new LikePost();
            $like->user_id = $user->id;
            $like->post_id = $request->post_id;

            if ($like->save()) {
                return response()->json(['message' => 'Você curtiu essa postagem', 'data' => $like], Response::HTTP_OK);
            } else {
                return response()->json(['message' => 'Ocorreu algum erro'], Response::HTTP_CONFLICT);
            }
        }
    }
}

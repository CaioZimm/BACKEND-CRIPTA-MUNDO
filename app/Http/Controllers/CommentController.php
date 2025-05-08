<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function index(){

        if (!Comment::first()) {
            return response()->json(['message' => 'Nenhum comentário encontrado'], Response::HTTP_NOT_FOUND);
        }

        $user = Auth::user();

        $comment = Comment::with('user:id,name,email')->withCount('likeComments')->get();

        foreach ($comment as $comments) {
            $comments->liked = $user->likeComments()->where('comment_id', $comments->id)->exists();
        }

        return response()->json(['data' => $comment], Response::HTTP_OK);
    }

    public function create() {}

    public function store(CommentRequest $request)
    {
        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' =>  $request->post_id,
            'description' =>  $request->description,
        ]);

        $comment = Comment::with('user')->find($comment->id);

        return response()->json(['data' => $comment], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $comment = Comment::with('user:id,name,email')->withCount('likeComments')->find($id);

        if (!$comment) {
            return response()->json(['erro' => 'Nada encontrado'], Response::HTTP_NOT_FOUND);
        }

        $liked = $user->likeComments()->where('comment_id', $id)->exists();
        $comment->like = $liked;

        return response()->json(['data' => $comment], Response::HTTP_OK);
    }

    public function edit(Comment $comment) {}

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['erro' => 'Nada encontrado'], Response::HTTP_NOT_FOUND);
        }

        if ($comment->user_id === $request->user()->id) {

            $comment->description = $request->description ?: $comment->description;
            $comment->save();

            return response()->json(
                [
                    'message' => 'Atualizado com sucesso',
                    'data' => $comment
                ],
                Response::HTTP_OK
            );
        } else {
            return response()->json(
                ['erro' => 'Você não possui permissão para editar esse comentário'],
                Response::HTTP_FORBIDDEN
            );
        }
    }

    public function destroy(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['erro' => 'Não foi possível realizar a exclusão desse comentário'], Response::HTTP_NOT_FOUND);
        }

        if ($comment->user_id === $request->user()->id) {

            $comment->likeComments()->delete();
            $comment->delete();
            
            return response()->json(['message' => 'Comentário excluído com sucesso!'], Response::HTTP_OK);

        } else {
            return response()->json(
                ['erro' => 'Você não possui permissão para deletar esse comentário'],
                Response::HTTP_FORBIDDEN
            );
        }
    }
}

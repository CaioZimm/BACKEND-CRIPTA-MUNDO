<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Access;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function index()
    {
        
        if (!Post::first()) {
            return response()->json(['erro' => 'Nada encontrado'], Response::HTTP_NO_CONTENT);
        }

        return response()->json(
            [
                'data' => Post::with(['chapters'])
                    ->withCount('likePosts', 'access', 'chapters')->orderBy('access_count', 'desc')
                    ->get()
            ],
            Response::HTTP_OK
        );
    }

    public function store(PostRequest $request)
    {
        $image = $request->file('image');
        $image_urn = $image->store('post/images', 'public');

        $url = url()->previous("/storage/$image_urn");

        $post = Post::create([
            'user_id' =>  $request->user()->id,
            'title' =>  $request->title,
            'caption' =>  $request->caption,
            'description' =>  $request->description,
            'image' => $url,
        ]);

        return response()->json(['data' => $post], Response::HTTP_CREATED);
    }

    public function show($id, Request $request)
    {
        $post = Post::with(['chapters', 'comments' => function ($like) {
            $like->withCount('likeComments')->with('user:id,name,email,photo');}])
            ->withCount('likePosts', 'access', 'chapters', 'comments')->find($id);

        if ($post === null) {
            return response()->json(['erro' => 'Nada encontrado'], Response::HTTP_NOT_FOUND);
        }

        $user = Auth::user();

        $liked = $user->likePosts()->where('post_id', $id)->exists();
        $post->like = $liked;

        foreach ($post->comments as $comment) {
            $comment->like = $user->likeComments()->where('comment_id', $comment->id)->exists();
        }

        if (!Access::where('user_id', $request->user()->id)->where('post_id', $id)->first()) {
            $access = new Access();
            $access->user_id = $user->id;
            $access->post_id = $post->id;

            $access->save();
        }

        return response()->json(['data' => $post], RESPONSE::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['erro' => 'Nada encontrado'], Response::HTTP_NOT_FOUND);
        }
        
        if ($request->hasFile('image')) {
            $image = $request->file('image'); 
    
            if ($post->image) {
                $oldimage = str_replace(url('/storage') . '/', '', $post->image);
                Storage::disk('public')->delete($oldimage);
            }

            $image_urn = $image->store('post/images', 'public'); 

            $url = url()->previous("/storage/{$image_urn}");
    
            $post->image = $url;
        }
            
        $post->title = $request->title ?: $post->title;
        $post->caption = $request->caption ?: $post->caption;
        $post->description = $request->description ?: $post->description;    
        $post->update();

        return response()->json(
            [
                'message' => 'Atualizado com sucesso!',
                'data' => $post
            ],
            Response::HTTP_OK
        );
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if ($post === null) {
            return response()->json(['erro' => 'Não foi possível realizar a exclusão dessa postagem'], Response::HTTP_NOT_FOUND);
        }

        Storage::disk('public')->delete(str_replace(
            url('/storage') . '/', '', $post->image));

        DB::table('like_comments')->whereIn('comment_id', $post->comments()->pluck('id'))->delete();
        $post->comments()->where('post_id', $id)->delete();
        $post->likePosts()->where('post_id', $id)->delete();
        $post->access()->where('post_id', $id)->delete();
        $post->chapters()->delete();

        $post->delete();
        return response()->json(['message' => 'Postagem excluída com sucesso!'], Response::HTTP_NO_CONTENT);
    }
}

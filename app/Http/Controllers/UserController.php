<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(){
        if (!User::first()) {
            return response()->json(['erro' => 'Nada encontrado'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => User::get()], Response::HTTP_OK);
    }

    public function store(UserRequest $request){}

    public function show($id)
    {
        $user =  User::select('id', 'name', 'email', 'photo')   
                    ->with(['likePosts'])
                    ->with(['comments' => function ($like) {
                        $like->withCount('likeComments');}])
                    ->find($id);
        
        if ($user === null) {
            return response()->json(['erro' => 'Nada encontrado'], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json($user[0], Response::HTTP_OK);
    }

    public function showProfile(Request $request){

        $token = PersonalAccessToken::findToken($request->bearerToken());

        if(!$token){
            return response()->json(['message' => 'Perfil não encontrado'], Response::HTTP_NOT_FOUND);
        }

        $user = User::select('id', 'name', 'email', 'photo')
                    ->withCount(['likePosts'])
                    ->with(['comments' => function ($like) {
                        $like->withCount('likeComments');}])
                    ->find($token);
        
        return response()->json($user[0], Response::HTTP_OK);
    }

    public function update(Request $request){
        
        $token = PersonalAccessToken::findToken($request->bearerToken());

        if ($token === null) {
            return response()->json(['erro' => 'Nada encontrado'], Response::HTTP_NOT_FOUND);
        }

        $user = User::select('id', 'name', 'email', 'photo')->find($token->tokenable_id);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            
            if ($user->photo != asset('storage/profile/profile.png')) {
                $oldphoto = str_replace(url('/storage') . '/', '', $user->photo);
                Storage::disk('public')->delete($oldphoto);
            }

            $image_urn = $photo->store('profile/images', 'public');

            $url = url()->previous("storage/{$image_urn}");
    
            $user->photo = $url;
        }

        $user->name = $request->name ?: $user->name;
        $user->email = $request->email ?: $user->email;
        $user->update();

        return response()->json([
            'message' => 'Atualizado com sucesso', 
            'data' => $user ], 
            Response::HTTP_OK);
    }
    
    public function destroy(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());

        if($token === null){
            return response()->json(['erro' => 'Não foi possível realizar a exclusão desse usuario'], Response::HTTP_NOT_FOUND);
        }

        $user = User::find($token->tokenable_id);

        if($user->id === $request->user()->id){

            if ($user->photo != asset('storage/profile/profile.png')) {
                $oldphoto = str_replace(url('/storage') . '/', '', $user->photo);
                Storage::disk('public')->delete($oldphoto);
            }

            $user->likePosts()->where('user_id', $user->id)->delete();
            $user->access()->where('user_id', $user->id)->delete();
            $user->likeComments()->whereIn('comment_id', DB::table('comments')->where('user_id', $user->id)->pluck('id'))->delete();
            $user->comments()->where('user_id', $user->id)->delete();

            $user->delete();
            
            return response()->json(['message' => 'Seu usuário foi excluído com sucesso!'], Response::HTTP_OK);
        } else {
            return response()->json(['message'=> 'Você não tem permissão para isso'], Response::HTTP_NOT_FOUND);
        }
    }
}

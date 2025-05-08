<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Symfony\Component\HttpFoundation\Response;

class UpdatePasswordController extends Controller
{
    public function updatePassword(Request $request){
        $request->validate([
            'password_current' => ['required'],
            'new_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
    
        $user = Auth::user();
    
        if (!Hash::check($request->password_current, $user->password)) {
            return response()->json(['message' => 'A senha atual está incorreta'], Response::HTTP_NOT_FOUND);
        }
    
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json(['message' => 'A nova senha deve ser diferente da anterior'], Response::HTTP_CONFLICT);
        }
    
        $user->password = Hash::make($request->new_password);
        $user->update();
    
        return response()->json(['message' => 'Senha atualizada com sucesso!'], Response::HTTP_OK);
    }
}

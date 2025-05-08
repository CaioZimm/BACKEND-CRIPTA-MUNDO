<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserRequest $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'photo' => asset('storage/profile/profile.png'),
            'password' => Hash::make($request->string('password')),
            'usertype' => $request->usertype ?? 'user',
        ]);

        if ($request->filled('usertype') && $request->usertype === 'admin') {
            $user['usertype'] = 'admin';
        }

        event(new Registered($user));

        Auth::login($user);

        return response()->json(['message' => 'Registrado com Sucesso!'], Response::HTTP_CREATED);
    }
}

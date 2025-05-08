<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required','email']);

        if(!$user = User::where('email', $request->email)->first()){
            return response()->json(['message' => "Email não encontrado"], Response::HTTP_NOT_FOUND);
        }

        Mail::to($user)->send(new ForgotPassword($user));

        return response()->json(['message' => 'Email enviado'], Response::HTTP_OK);
    }
}
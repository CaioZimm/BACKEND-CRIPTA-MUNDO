<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmationResetPassword as MailConfirmationResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Symfony\Component\HttpFoundation\Response;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'max:6', 'min:6'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $verify = DB::select('select * from password_reset_tokens where token = ?', [$request->token]);

        if ($verify) {
            $tokenCreated = Carbon::parse($verify[0]->created_at);
    
            if ($tokenCreated->addMinutes(30)->isPast()) {
                return response()->json(['message' => 'Token expirado.'], Response::HTTP_REQUEST_TIMEOUT);
            }
        }

        if(!$verify){
            return response()->json(['message' => 'Token não encontrado'], Response::HTTP_NOT_FOUND);
        }

        $user = User::where('email', '=', $verify[0]->email)->first();

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'A nova senha deve ser diferente da anterior'], Response::HTTP_CONFLICT);
        }

        $user->update([
            'password' => $request->password
        ]);

        DB::delete("delete from password_reset_tokens where email = ?", [$user->email]);

        Mail::to($user->email)->send(new MailConfirmationResetPassword($user->name));

        return response()->json(['message' => 'Senha resetada com sucesso!'], Response::HTTP_OK);
    }
}

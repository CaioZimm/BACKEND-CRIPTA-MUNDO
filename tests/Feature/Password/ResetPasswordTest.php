<?php

namespace Tests\Feature\Password;

use App\Mail\ConfirmationResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    public function test_reset_password_success(){
        Mail::fake();

        $tableToken = DB::table('password_reset_tokens')->first();
        
        if (!$tableToken) {
            $user = User::inRandomOrder()->first();

            $token = '123456';
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now()
            ]);
        } else {
            $token = $tableToken->token;
            $user = User::where('email', $tableToken->email)->first();
        }

        $updatedPassword = [
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        $response = $this->postJson("/reset-password", $updatedPassword);

        $response->assertStatus(200)->assertJson([
            'message' => 'Senha resetada com sucesso!'
        ]);

        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);

        Mail::assertSent(ConfirmationResetPassword::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_reset_password_wrong_token(){

        $tableToken = DB::table('password_reset_tokens')->first();
        
        if (!$tableToken) {
            $user = User::inRandomOrder()->first();

            $token = '123456';
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now()
            ]);
        } else {
            $token = $tableToken->token;
            $user = User::where('email', $tableToken->email)->first();
        }

        $updatedPassword = [
            'token' => '777777',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        $response = $this->postJson("/reset-password", $updatedPassword);

        $response->assertStatus(404)->assertJson([
            'message' => 'Token não encontrado'
        ]);
    }

    public function test_reset_password_same_password(){
        $user = User::factory()->create([
            'password' => Hash::make('password')
        ]);

        $token = '123456';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()
        ]);

        $updatedPassword = [
            'token' => $token,
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson("/reset-password", $updatedPassword);

        $response->assertStatus(409)->assertJson([
            'message' => 'A nova senha deve ser diferente da anterior'
        ]);
    }
}
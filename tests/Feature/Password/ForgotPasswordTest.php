<?php

namespace Tests\Feature\Password;

use App\Mail\ForgotPassword;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    public function test_forgot_password_send_email(){
        Mail::fake();
        
        $user = User::inRandomOrder()->first();

        if (!$user) {
            $user = User::factory()->create();
        }

        $response = $this->postJson("/forgot-password", [
            'email' => $user->email
        ]);

        $response->assertStatus(200)->assertJson(['message' => 'Email enviado']);

        Mail::assertSent(ForgotPassword::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_forgot_password_email_not_found(){

        $response = $this->postJson("/forgot-password", [
            'email' => 'x@gmail.com'
        ]);

        $response->assertStatus(404)->assertJson(['message' => "Email não encontrado"]);
    }
}
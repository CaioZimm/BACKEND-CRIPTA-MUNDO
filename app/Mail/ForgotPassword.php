<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $token;
    public function __construct(User $user)
    {
        $this->token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        if(DB::select('select * from password_reset_tokens where email  = ?', [$user->email])){
            DB::update("update password_reset_tokens set token = ?, created_at = ? WHERE email = ?", [$this->token, Carbon::now(), $user->email]);
        }else{
            DB::insert('insert into password_reset_tokens (email, token, created_at) values (?, ?, ?)', [$user->email, $this->token, Carbon::now()]);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Esqueci a senha',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return new Content(
            view: 'emails/forgot_password',
            with: [
                'token' => $this->token,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

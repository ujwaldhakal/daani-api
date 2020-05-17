<?php

namespace App\Mail;

use App\Repos\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        //
        $this->user = $user;
    }

    public function getVerificationLink() : string
    {
        $url = env('FRONTEND_APP_URL').'/verify?token='.base64_encode($this->user->getApiToken());

        Log::info("verification token generated",[
            'userId' => $this->user->getId()
        ]);

        return $url;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.auth.email-verification')->with(['verificationLink' => $this->getVerificationLink()]);
    }
}
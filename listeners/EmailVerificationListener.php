<?php

namespace App\listeners;

use App\core\mails\Mail;


class EmailVerificationListener
{

    public function handle($user,$token)
    {
        $email = Mail::make();
        $email->from('okirimkadiro@gmail.com')
            ->to($user->email)
            ->subject('verify-email')
            ->view('confirmation_mail.php', ['token' => $token])
            ->send();
    }

}
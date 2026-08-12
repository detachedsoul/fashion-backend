<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Build a signed link to our own API. The email link is the literal
     * signed route the user clicks; the controller verifies the signature,
     * marks the email verified, then 302-redirects into the Next.js
     * frontend. This keeps Laravel's built-in signature verification
     * completely untouched (no manual signature reconstruction needed).
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'api.v1.auth.email.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify your email address')
            ->markdown('mail.auth.verify-email', [
                'url' => $this->verificationUrl($notifiable),
                'name' => $notifiable->name,
            ]);
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the CURRENT (still-active) email the moment a change is
 * requested - the real `email` column hasn't moved yet (see pending_email),
 * so this account is still reachable/recoverable through this address for
 * the entire confirmation window.
 */
class EmailChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $oldEmail,
        public readonly string $requestedEmail,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Email change requested on your account')
            ->markdown('mail.auth.email-changed', [
                'oldEmail' => $this->oldEmail,
                'requestedEmail' => $this->requestedEmail,
                'supportUrl' => rtrim(config('app.frontend_url'), '/').'/contact',
            ]);
    }
}

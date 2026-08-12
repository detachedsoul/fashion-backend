<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the CURRENT (still-active) admin email the moment a change is
 * requested - mirrors App\Notifications\EmailChangedNotification. The live
 * `email` column hasn't moved (see pending_email), so this account is still
 * reachable through this address for the whole confirmation window.
 */
class AdminEmailChangedNotification extends Notification implements ShouldQueue
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
            ->subject('Email change requested on your admin account')
            ->markdown('mail.admin.email-changed', [
                'oldEmail' => $this->oldEmail,
                'requestedEmail' => $this->requestedEmail,
                'supportUrl' => rtrim(config('app.frontend_url'), '/').'/contact',
            ]);
    }
}

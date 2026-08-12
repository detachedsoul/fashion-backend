<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your password was reset')
            ->markdown('mail.auth.password-reset-confirmation', [
                'name' => $notifiable->name,
                'supportUrl' => rtrim(config('app.frontend_url'), '/').'/contact',
            ]);
    }
}

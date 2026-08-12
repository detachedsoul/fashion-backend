<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * Sent via on-demand routing (Notification::route('mail', $pendingEmail))
 * since $user->email is still the OLD address at this point - $user->notify()
 * would deliver to the wrong inbox.
 */
class ConfirmEmailChangeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $userId,
        public readonly string $pendingEmail,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'api.v1.auth.email.confirm-change',
            Carbon::now()->addMinutes(60),
            [
                'id' => $this->userId,
                'hash' => sha1($this->pendingEmail),
            ]
        );

        return (new MailMessage)
            ->subject('Confirm your new email address')
            ->markdown('mail.auth.confirm-email-change', [
                'url' => $url,
            ]);
    }
}

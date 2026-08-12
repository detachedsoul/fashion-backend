<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * Sent via on-demand routing to the pending address - $admin->email is
 * still the OLD address at this point.
 */
class AdminConfirmEmailChangeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $adminId,
        public readonly string $pendingEmail,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'api.v1.admin.email.confirm-change',
            Carbon::now()->addMinutes(60),
            [
                'id' => $this->adminId,
                'hash' => sha1($this->pendingEmail),
            ]
        );

        return (new MailMessage)
            ->subject('Confirm your new admin email address')
            ->markdown('mail.admin.confirm-email-change', [
                'url' => $url,
            ]);
    }
}

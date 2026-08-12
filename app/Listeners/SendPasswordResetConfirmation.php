<?php

namespace App\Listeners;

use App\Models\Admin;
use App\Notifications\Admin\AdminPasswordResetConfirmationNotification;
use App\Notifications\PasswordResetConfirmationNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Same reasoning as SendEmailVerifiedConfirmation - Laravel's built-in
 * PasswordReset event is generic across both models, so this one listener
 * branches rather than risking duplicate/mismatched notifications.
 */
class SendPasswordResetConfirmation implements ShouldQueue
{
    public function handle(PasswordReset $event): void
    {
        $notification = $event->user instanceof Admin
            ? new AdminPasswordResetConfirmationNotification
            : new PasswordResetConfirmationNotification;

        $event->user->notify($notification);
    }
}

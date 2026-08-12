<?php

namespace App\Listeners;

use App\Models\Admin;
use App\Notifications\Admin\AdminEmailVerifiedNotification;
use App\Notifications\EmailVerifiedNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Auto-discovered by Laravel (typed Verified param, handle() method).
 * Laravel's built-in Verified event is generic - it fires identically
 * whether a User or an Admin just verified their email (both implement the
 * same MustVerifyEmail contract), so this one listener branches on the
 * concrete model rather than needing two separate listeners racing to
 * handle the same event (which would risk sending both emails to whoever
 * verified).
 */
class SendEmailVerifiedConfirmation implements ShouldQueue
{
    public function handle(Verified $event): void
    {
        $notification = $event->user instanceof Admin
            ? new AdminEmailVerifiedNotification
            : new EmailVerifiedNotification;

        $event->user->notify($notification);
    }
}

<?php

namespace App\Listeners;

use App\Models\ReferralRelationship;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Auto-discovered by Laravel (typed Registered param, handle() method) - no
 * manual registration needed in a provider.
 *
 * This only records *that* a referral happened (level 1) and walks the
 * existing chain up to build higher-level rows for team bonuses, capped at a
 * sane depth. It intentionally does NOT create any commission or charge any
 * fee here - referral_commissions rows are only ever created later, off the
 * back of a real completed order (see the Orders/Referrals domain build).
 */
class LinkReferralRelationship implements ShouldQueue
{
    public function handle(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        if (! $user->referred_by_user_id) {
            return;
        }

        $maxDepth = (int) config('referrals.max_team_bonus_depth', 3);

        $referrer = User::find($user->referred_by_user_id);
        $level = 1;

        while ($referrer && $level <= $maxDepth) {
            ReferralRelationship::firstOrCreate([
                'referrer_id' => $referrer->id,
                'referee_id' => $user->id,
                'level' => $level,
            ]);

            $referrer = $referrer->referred_by_user_id
                ? User::find($referrer->referred_by_user_id)
                : null;

            $level++;
        }
    }
}

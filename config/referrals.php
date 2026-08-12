<?php

return [
    // How many levels deep a referral chain is recorded for team-bonus
    // purposes. Kept deliberately small - see the architecture notes on
    // why deep multi-level payout chains are a regulatory red flag even
    // when, as here, they're funded by real sales rather than signup fees.
    'max_team_bonus_depth' => env('REFERRALS_MAX_TEAM_BONUS_DEPTH', 3),
];

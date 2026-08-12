<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Holds a requested-but-not-yet-confirmed new email address. The
            // real `email` column only ever changes once the pending address
            // is confirmed via a signed link - see
            // EmailVerificationController::confirmChange(). This closes an
            // account-takeover window where a stolen session/token alone
            // could otherwise flip `email` instantly and let an attacker
            // request a password reset on an address they control before
            // the real owner even sees the "email change requested" alert.
            $table->string('pending_email')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pending_email');
        });
    }
};

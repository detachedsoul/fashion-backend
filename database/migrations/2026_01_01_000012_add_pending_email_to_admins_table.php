<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Same purpose as users.pending_email - see that migration's
            // comment. An admin account is an even higher-value target, so
            // the same "never write the live email column until the new
            // address is confirmed" protection matters at least as much here.
            $table->string('pending_email')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('pending_email');
        });
    }
};

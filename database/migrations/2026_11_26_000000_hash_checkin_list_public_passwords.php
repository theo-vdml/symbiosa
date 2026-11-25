<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Check-in list passwords used to be stored in plain text.
 * Hash every existing value that is not already a password hash.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('checkin_lists')
            ->whereNotNull('public_url_password')
            ->where('public_url_password', '!=', '')
            ->orderBy('id')
            ->each(function (object $list) {
                if (password_get_info($list->public_url_password)['algo'] !== null) {
                    return;
                }

                DB::table('checkin_lists')
                    ->where('id', $list->id)
                    ->update(['public_url_password' => Hash::make($list->public_url_password)]);
            });

        // An empty string meant "no password"; store it as null.
        DB::table('checkin_lists')
            ->where('public_url_password', '')
            ->update(['public_url_password' => null]);
    }

    public function down(): void
    {
        // Irreversible: plain-text passwords cannot be recovered from their hashes.
    }
};

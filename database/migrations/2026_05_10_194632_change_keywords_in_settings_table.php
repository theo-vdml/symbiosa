<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Settings table payload is already text/json, so we just need to make sure
        // existing data for keywords is converted to empty array if null
        $groups = ['homepage', 'about_page'];
        foreach ($groups as $group) {
            DB::table('settings')
                ->where('group', $group)
                ->where('name', 'seo_keywords')
                ->update(['payload' => json_encode([])]);
        }
    }

    public function down(): void
    {
    }
};

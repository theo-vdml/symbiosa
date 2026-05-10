<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $groups = ['homepage', 'about_page'];
        $fields = [
            'seo_title',
            'seo_description',
            'seo_keywords',
            'seo_robots',
            'seo_canonical_url',
            'seo_og_title',
            'seo_og_description',
            'seo_og_image',
            'seo_og_type',
            'seo_twitter_card',
            'seo_twitter_title',
            'seo_twitter_description',
            'seo_twitter_image',
            'seo_json_ld',
        ];

        foreach ($groups as $group) {
            foreach ($fields as $field) {
                $exists = DB::table('settings')
                    ->where('group', $group)
                    ->where('name', $field)
                    ->exists();

                if (!$exists) {
                    DB::table('settings')->insert([
                        'group' => $group,
                        'name' => $field,
                        'payload' => json_encode(null),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // No need to undo this fix
    }
};

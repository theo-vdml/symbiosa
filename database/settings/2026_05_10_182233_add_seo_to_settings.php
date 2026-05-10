<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
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
                $this->migrator->add("{$group}.{$field}", null);
            }
        }
    }

    public function down(): void
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
                $this->migrator->delete("{$group}.{$field}");
            }
        }
    }
};

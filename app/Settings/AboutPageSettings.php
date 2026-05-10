<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutPageSettings extends Settings
{
    public ?array $sections;

    public ?string $seo_title;
    public ?string $seo_description;
    public ?array $seo_keywords;
    public ?string $seo_robots;
    public ?string $seo_canonical_url;
    public ?string $seo_og_title;
    public ?string $seo_og_description;
    public ?string $seo_og_image;
    public ?string $seo_og_type;
    public ?string $seo_twitter_card;
    public ?string $seo_twitter_title;
    public ?string $seo_twitter_description;
    public ?string $seo_twitter_image;
    public ?array $seo_json_ld;

    public static function group(): string
    {
        return 'about_page';
    }
}

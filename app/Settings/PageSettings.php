<?php

namespace App\Settings;

use App\Services\SeoProcessor;
use Spatie\LaravelSettings\Settings;

abstract class PageSettings extends Settings
{
    /**
     * SEO-related fields that can be defined in settings.
     * These will be processed by the SeoProcessor to generate the final SEO metadata.
     */
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

    /**
     * Define fallback values for SEO fields.
     *
     * The keys should correspond to SEO fields (e.g., 'title', 'description').
     * The values can be either a single column name or an array of column names
     * to check in order for a non-empty value.
     *
     * @return array
     */
    public function getSeoFallbacks(): array
    {
        return [];
    }

    /**
     * Define default values for SEO fields if both the base data and fallbacks are empty.
     *
     * The keys should correspond to SEO fields (e.g., 'title', 'description').
     *
     * @return array
     */
    public function getSeoDefaults(): array
    {
        return [];
    }

    /**
     * Get the processed SEO data from settings.
     *
     * @return array
     */
    public function getSeoData(): array
    {
        $baseData = collect(get_object_vars($this))
            ->filter(fn(mixed $value, string|int $key) => str_starts_with($key, 'seo_'))
            ->mapWithKeys(fn(mixed $value, string|int $key) => [str_replace('seo_', '', $key) => $value])
            ->toArray();

        return SeoProcessor::make(
            baseData: $baseData,
            fallbacks: $this->getSeoFallbacks(),
            defaults: $this->getSeoDefaults(),
            source: $this
        );
    }
}

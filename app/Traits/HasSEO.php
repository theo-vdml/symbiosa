<?php

namespace App\Traits;

use App\Models\Seo;
use App\Services\SeoProcessor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property Seo $seo
 * @mixin Model
 */
trait HasSEO
{
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
     * Get the polymorphic SEO relationship.
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'model');
    }

    /**
     * Get the processed SEO data for the model.
     *
     * @return array
     */
    public function getSeoData(): array
    {
        return SeoProcessor::make(
            baseData: $this->seo?->toArray() ?? [],
            fallbacks: $this->getSeoFallbacks(),
            defaults: $this->getSeoDefaults(),
            source: $this
        );
    }
}

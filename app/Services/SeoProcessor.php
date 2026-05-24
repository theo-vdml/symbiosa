<?php

namespace App\Services;

/**
 * Class SeoProcessor
 *
 * Handles the logic for merging, resolving, and transforming SEO metadata
 * from different sources like Eloquent models or application settings.
 */
class SeoProcessor
{
    public static function make(array $baseData, array $fallbacks = [], array $defaults = [], mixed $source = null): array
    {
        $data = [];
        $fields = static::getSeoFields();

        foreach ($fields as $field) {
            $value = $baseData[$field] ?? null;

            if (empty($value) && !empty($fallbacks[$field] ?? null)) {
                $value = static::resolveFromSource($fallbacks[$field], $source);
            }

            if (empty($value)) {
                $value = $defaults[$field] ?? null;

                if (is_callable($value)) {
                    $value = $value($source);
                }
            }

            $data[$field] = $value;
        }

        $data = static::applyTransformations($data);

        return $data;
    }

    protected static function resolveFromSource(mixed $columns, mixed $source): string|null
    {
        if (!$source) return null;

        $columns = is_array($columns) ? $columns : [$columns];

        foreach ($columns as $column) {
            $val = $source->{$column} ?? null;
            if (!empty($val)) return (string) $val;
        }

        return null;
    }

    /**
     * Clean and format specific field types (e.g., converting arrays to strings).
     *
     * @param array $data
     * @return array
     */
    protected static function applyTransformations(array $data): array
    {
        if (isset($data['keywords']) && is_array($data['keywords'])) {
            $data['keywords'] = implode(', ', $data['keywords']);
        }

        if (isset($data['json_ld']) && is_array($data['json_ld'])) {
            $data['json_ld'] = json_encode($data['json_ld']);
        }

        return $data;
    }

    /**
     * Get the list of supported SEO fields.
     *
     * @return array<int, string>
     */
    protected static function getSeoFields(): array
    {
        return [
            'title',
            'description',
            'keywords',
            'robots',
            'canonical_url',
            'og_title',
            'og_description',
            'og_image',
            'og_type',
            'twitter_card',
            'twitter_title',
            'twitter_description',
            'twitter_image',
            'json_ld'
        ];
    }
}

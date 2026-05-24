<?php

namespace App\Filament\Shared\Services;

use Filament\Schemas\Components\Utilities\Get;
use Livewire\Component as Livewire;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SeoPreviewService
{
    /**
     * Calcule la valeur finale (Saisie > Fallback > Default)
     */
    public static function getComputedValue(Get $get, Livewire $livewire, string $field, string $p = ""): string
    {
        $manual = $get("{$p}{$field}");
        if (!empty($manual)) return $manual;

        return static::getPlaceholder($get, $livewire, $field);
    }

    /**
     * Calcule le placeholder (Fallback > Default)
     */
    public static function getPlaceholder(Get $get, Livewire $livewire, string $field): string
    {
        $source = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : $livewire;
        if (!$source) return '';

        // 1. Fallbacks (Dynamic fields)
        $fallbacks = method_exists($source, 'getSeoFallbacks') ? $source->getSeoFallbacks() : [];
        if (!empty($fallbacks[$field])) {
            $columns = is_array($fallbacks[$field]) ? $fallbacks[$field] : [$fallbacks[$field]];
            foreach ($columns as $column) {
                // Check form state then object property
                $val = $get("../../{$column}") ?? $get($column) ?? $source->{$column} ?? null;
                if (!empty($val)) return strip_tags((string) $val);
            }
        }

        // 2. Defaults (Static strings or callables)
        $defaults = method_exists($source, 'getSeoDefaults') ? $source->getSeoDefaults() : [];
        $default = $defaults[$field] ?? '';

        if (is_callable($default)) {
            return (string) $default($source);
        }

        return (string) $default;
    }

    /**
     * Résout l'URL de l'image pour la preview
     */
    public static function getImageUrl(Get $get, Livewire $livewire, string $p = ""): string
    {
        // Priority 1: Current Upload
        $imageState = $get("{$p}og_image");
        if ($url = static::extractUrl($imageState)) return $url;

        // Priority 2: Fallback field
        $source = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : $livewire;
        if (!$source) return "https://placehold.co/1200x650?text=No+Image";
        $fallbacks = method_exists($source, 'getSeoFallbacks') ? $source->getSeoFallbacks() : [];
        $fallbackField = $fallbacks['og_image'] ?? null;

        if ($fallbackField) {
            $fallbackState = $get("../../{$fallbackField}") ?? $source->{$fallbackField} ?? null;
            if ($url = static::extractUrl($fallbackState)) return $url;
        }

        // Priority 3: Defaults
        $defaults = method_exists($source, 'getSeoDefaults') ? $source->getSeoDefaults() : [];
        $defaultImage = $defaults['og_image'] ?? null;

        if (is_callable($defaultImage)) {
            $defaultImage = $defaultImage($source);
        }

        if ($url = static::extractUrl($defaultImage)) return $url;

        return "https://placehold.co/1200x650?text=No+Image";
    }

    private static function extractUrl(mixed $state): ?string
    {
        if ($state instanceof TemporaryUploadedFile) {
            try {
                return $state->temporaryUrl();
            } catch (\Exception $e) {
                return null;
            }
        }
        if (is_array($state)) {
            $file = reset($state);
            return static::extractUrl($file);
        }
        if (is_string($state) && !empty($state)) {
            return str_starts_with($state, 'http') ? $state : asset("storage/{$state}");
        }
        return null;
    }
}

<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Trait HasSlug
 *
 * Automatically generates a unique slug upon saving if the slug field is empty.
 *
 * @mixin Model
 */
trait HasSlug
{
    /**
     * Boot the trait.
     *
     * Laravel automatically calls this method when the model is booted.
     */
    public static function bootHasSlug(): void
    {
        static::saving(function (Model $model) {
            $source = $model->getSlugSource();
            $field = $model->getSlugField();

            if (empty($model->{$field}) && !empty($model->{$source})) {
                $slug = Str::slug($model->{$source});
                $model->{$field} = $model->generateUniqueSlug($slug);
            }
        });
    }

    /**
     * Get the attribute name to generate the slug from.
     */
    public function getSlugSource(): string
    {
        // Utilise la propriété si elle existe sur le modèle, sinon 'name' par défaut
        return $this->slugSource ?? 'name';
    }

    /**
     * Get the column name where the slug is stored.
     */
    public function getSlugField(): string
    {
        // Utilise la propriété si elle existe sur le modèle, sinon 'slug' par défaut
        return $this->slugField ?? 'slug';
    }

    /**
     * Generate a unique slug by checking the database for existing records.
     */
    private function generateUniqueSlug(string $slug): string
    {
        $field = $this->getSlugField();
        $originalSlug = $slug;
        $count = 1;

        while (
            static::where($field, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey())
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}

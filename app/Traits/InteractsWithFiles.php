<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Trait InteractsWithFiles
 *
 * Automatically handles the deletion of files from storage when an Eloquent model
 * is updated or deleted. It supports multiple disks and handles Soft Deletes
 * by only removing files during a force delete.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait InteractsWithFiles
{
    /**
     * Define the model attributes that contain file paths.
     *
     * Format:
     * - ['column_name'] (defaults to 'public' disk)
     * - ['column_name' => 's3'] (specifies a custom disk)
     *
     * @return array<int|string, string>
     */
    abstract public function fileAttributes(): array;

    /**
     * Determine if the trait can be safely booted for the current class.
     *
     * Verifies that:
     * 1. The class is a valid Eloquent Model.
     * 2. The required 'fileAttributes' method has been implemented.
     *
     * @return bool
     */
    protected static function canBootInteractsWithFiles(): bool
    {
        return is_subclass_of(static::class, Model::class)
            && method_exists(static::class, 'fileAttributes');
    }

    /**
     * Boot the trait.
     *
     * Registers 'saving' and 'deleting' Eloquent hooks to manage file lifecycles.
     * Only executes if the using class is an instance of Illuminate\Database\Eloquent\Model.
     *
     * @return void
     */
    protected static function bootInteractsWithFiles(): void
    {
        if (!static::canBootInteractsWithFiles()) {
            return;
        }

        /**
         * Handle file cleanup during model updates.
         * Deletes the old file if the attribute has changed.
         *
         */
        static::updated(function (Model $model) {
            if (method_exists($model, 'getNormalizedFileAttributes')) {
                foreach ($model->getNormalizedFileAttributes() as $field => $disk) {
                    if ($model->isDirty($field) && $model->getOriginal($field)) {
                        Storage::disk($disk)->delete($model->getOriginal($field));
                    }
                }
            }
        });

        /**
         * Handle file cleanup during model deletion.
         *
         */
        static::deleted(function (Model $model) {
            if (method_exists($model, 'getNormalizedFileAttributes')) {
                foreach ($model->getNormalizedFileAttributes() as $field => $disk) {
                    if ($model->$field) {
                        Storage::disk($disk)->delete($model->$field);
                    }
                }
            }
        });
    }

    /**
     * Normalizes the fileAttributes configuration.
     * Converts a simple list like ['poster'] into a keyed array like ['poster' => 'public'].
     *
     * @return array<string, string>
     */
    protected function getNormalizedFileAttributes(): array
    {
        $normalized = [];
        foreach ($this->fileAttributes() as $key => $value) {
            if (is_int($key)) {
                $normalized[$value] = 'public';
            } else {
                $normalized[$key] = $value;
            }
        }
        return $normalized;
    }
}

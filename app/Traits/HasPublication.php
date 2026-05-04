<?php

namespace App\Traits;

use App\Enums\PublicationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasPublication
{
    public static function bootHasPublication(): void
    {
        /** @var Model $this */
        static::saving(function ($model) {
            if ($model->status === null) {
                $model->status = PublicationStatus::Draft;
            }

            if ($model->status !== PublicationStatus::Draft && $model->published_at === null) {
                $model->published_at = now();
            }

            if ($model->status === PublicationStatus::Draft) {
                $model->published_at = null;
            }
        });
    }

    public function scopeDraft(Builder $query): void
    {
        $query->where('status', PublicationStatus::Draft);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', PublicationStatus::Published)
            ->where('published_at', '<=', now());
    }

    public function scopeScheduled(Builder $query): void
    {
        $query->where('status', PublicationStatus::Published)
            ->where('published_at', '>', now());
    }

    public function scopeArchived(Builder $query): void
    {
        $query->where('status', PublicationStatus::Archived);
    }
}

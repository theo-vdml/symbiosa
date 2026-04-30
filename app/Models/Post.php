<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'thumbnail',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if ($post->status !== PostStatus::Draft && $post->published_at === null) {
                $post->published_at = now();
            }

            if ($post->status === PostStatus::Draft) {
                $post->published_at = null;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeDraft(Builder $query): void
    {
        $query->where('status', PostStatus::Draft);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', PostStatus::Published)
            ->where('published_at', '<=', now());
    }

    public function scopeScheduled(Builder $query): void
    {
        $query->where('status', PostStatus::Published)
            ->where('published_at', '>', now());
    }

    public function scopeArchived(Builder $query): void
    {
        $query->where('status', PostStatus::Archived);
    }
}

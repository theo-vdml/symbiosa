<?php

namespace App\Models;

use App\Enums\PublicationStatus;
use App\Traits\HasPublication;
use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasPublication, HasSEO, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->withResponsiveImages()
            ->useDisk('r2');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(600)
            ->sharpen(10)
            ->performOnCollections('cover');
    }

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => PublicationStatus::class,
        'published_at' => 'datetime',
    ];

    protected $appends = [
        'cover_url',
        'cover_responsive',
        'thumbnail_url',
    ];

    public function getCoverUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover');
    }

    public function getCoverResponsiveAttribute(): array
    {
        $media = $this->getFirstMedia('cover');
        return $media ? [
            'src' => $media->getUrl(),
            'srcset' => $media->getSrcset(),
        ] : [];
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover', 'thumbnail');
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getSeoDefaults(): array
    {
        return [
            "title" => fn($record) => $record->title . ' - ' . config('app.name'),
            "canonical_url" => fn($record) => route('news.show', $record->slug),
            "twitter_card" => "summary_large_image",
        ];
    }

    public function getSeoFallbacks(): array
    {
        return [
            "description" => 'excerpt',
            "og_image" => 'media:cover',
        ];
    }
}

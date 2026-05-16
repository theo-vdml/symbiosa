<?php

namespace App\Models;

use App\Enums\PublicationStatus;
use App\Traits\HasPublication;
use App\Traits\InteractsWithFiles;
use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasPublication, InteractsWithFiles, HasSEO;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'thumbnail',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => PublicationStatus::class,
        'published_at' => 'datetime',
    ];

    public function fileAttributes(): array
    {
        return [
            'thumbnail' => 'public',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getSeoDefaults(): array
    {
        return [
            "title" => "Un article symbiosa",
            "twitter_card" => "summary_large_image",
        ];
    }

    public function getSeoFallbacks(): array
    {
        return [
            "title" => ["title", "slug"],
            "description" => ["excerpt", "title"],
            "og_title" => ["title", "slug"],
            "og_description" => ["excerpt", "title"],
            "og_image" => 'thumbnail',
        ];
    }

}

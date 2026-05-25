<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AboutPageSection extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'about_page_id',
        'title',
        'content',
        'sort_order',
    ];

    public function aboutPage(): BelongsTo
    {
        return $this->belongsTo(AboutPage::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useDisk('r2');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Seo extends Model
{
    protected $fillable = [
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
        'json_ld',
    ];

    protected $casts = [
        'json_ld' => 'array',
        'keywords' => 'array',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}

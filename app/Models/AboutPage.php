<?php

namespace App\Models;

use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AboutPage extends Model implements HasMedia
{
    use HasSEO, InteractsWithMedia;

    protected $fillable = [];

    public function sections(): HasMany
    {
        return $this->hasMany(AboutPageSection::class)->orderBy('sort_order');
    }

    public function getSeoDefaults(): array
    {
        return [
            'title' => fn() => "À propos - " . config('app.name'),
            "canonical_url" => fn() => route('about'),
        ];
    }
}

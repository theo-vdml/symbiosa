<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LegalPage extends Model
{
    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'is_footer',
        'requires_acceptance',
    ];

    protected $casts = [
        'is_footer' => 'boolean',
        'requires_acceptance' => 'boolean',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(LegalPageVersion::class)->orderByDesc('version_number');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(LegalPageVersion::class)->latestOfMany('version_number');
    }

    protected function getSlugSource(): string
    {
        return 'title';
    }

    public function scopeInFooter($query)
    {
        return $query->where('is_footer', true);
    }

    public function scopeRequiresAcceptance($query)
    {
        return $query->where('requires_acceptance', true);
    }
}

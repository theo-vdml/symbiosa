<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $slugSource = 'name';

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

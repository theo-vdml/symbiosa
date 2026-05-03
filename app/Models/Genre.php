<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class Genre extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    protected static function booted()
    {
        static::saving(function ($genre) {
            if (empty($genre->slug)) {
                $slug = Str::slug($genre->name);
                $genre->slug = static::generateUniqueSlug($slug, $genre->id);
            }
        });
    }

    private static function generateUniqueSlug(string $slug, ?int $id): string
    {
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}

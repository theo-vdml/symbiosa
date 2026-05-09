<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasSlug;

    protected $slugSource = 'name';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $guarded = [];

    public function events()
    {
        return $this->belongsToMany(Event::class)->withPivot('sort_order');
    }
}

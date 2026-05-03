<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArtistEvent extends Pivot
{
    protected $table = 'artist_event';

    public $incrementing = true;

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

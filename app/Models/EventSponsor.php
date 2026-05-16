<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventSponsor extends Pivot
{
    protected $table = 'event_sponsor';

    public $incrementing = true;

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }
}

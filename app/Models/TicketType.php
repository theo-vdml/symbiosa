<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'description',
        'capacity',
        'sold_count',
        'reserved_count',
        'available_from',
        'max_per_order',
        'sort_order'
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'capacity' => 'integer',
        'sold_count' => 'integer',
        'reserved_count' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(TicketPrice::class)->orderBy('sort_order');
    }
}

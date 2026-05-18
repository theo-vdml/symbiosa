<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class CheckinList extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'public_url_token',
        'public_url_password',
    ];

    protected $hidden = [
        'public_url_password',
    ];

    protected static function booted()
    {
        static::creating(function ($checkinList) {
            if (empty($checkinList->public_url_token)) {
                $checkinList->public_url_token = Str::random(32);
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketTypes(): MorphToMany
    {
        return $this->morphedByMany(TicketType::class, 'reservable', 'checkin_list_reservables');
    }

    public function addons(): MorphToMany
    {
        return $this->morphedByMany(EventAddon::class, 'reservable', 'checkin_list_reservables');
    }
}

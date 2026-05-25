<?php

namespace App\Models;

use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Model;

class EventsPage extends Model
{
    use HasSEO;

    protected $fillable = [
        'preheading',
        'heading',
        'description',
    ];

    public function getSeoDefaults(): array
    {
        return [
            'title' => ($this->heading ?? 'Événements à venir') . ' - ' . config('app.name'),
            "canonical_url" => fn() => route('events.index'),
        ];
    }

    public function getSeoFallbacks(): array
    {
        return [
            'description' => 'description'
        ];
    }
}

<?php

namespace App\Models;

use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Model;

class LostTicketPage extends Model
{
    use HasSEO;

    protected $fillable = [
        'preheading',
        'heading',
        'description',
        'help_items',
    ];

    protected $casts = [
        'help_items' => 'array',
    ];

    public function getSeoDefaults(): array
    {
        return [
            'title' => fn() => ($this->heading ?? 'Support') . ' - ' . config('app.name'),
            "canonical_url" => fn() => route('lost-tickets.show'),
        ];
    }

    public function getSeoFallbacks(): array
    {
        return [
            'description' => 'description'
        ];
    }
}

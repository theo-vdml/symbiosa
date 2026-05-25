<?php

namespace App\Models;

use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Model;

class ArchivePage extends Model
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
            'title' => ($this->heading ?? 'Nos Archives') . ' - ' . config('app.name'),
            "canonical_url" => fn() => route('archives'),
        ];
    }

    public function getSeoFallbacks(): array
    {
        return [
            'description' => 'description'
        ];
    }
}

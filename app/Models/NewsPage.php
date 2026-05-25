<?php

namespace App\Models;

use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Model;

class NewsPage extends Model
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
            'title' => ($this->heading ?? 'Actualités') . ' - ' . config('app.name'),
            "canonical_url" => fn() => route('news.index'),
        ];
    }

    public function getSeoFallbacks(): array
    {
        return [
            'description' => 'description'
        ];
    }
}

<?php

namespace App\Models;

use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Model;

class ContactPage extends Model
{
    use HasSEO;

    protected $fillable = [
        'heading',
        'subheading',
        'description',
        'faq_heading',
        'faq_description',
        'faq_items',
        'email_heading',
    ];

    protected $casts = [
        'faq_items' => 'array',
    ];

    public function getSeoDefaults(): array
    {
        return [
            'title' => ($this->heading ?? 'Contact') . ' - ' . config('app.name'),
            "canonical_url" => fn() => route('contact'),
        ];
    }

    public function getSeoFallbacks(): array
    {
        return [
            'description' => 'description'
        ];
    }
}

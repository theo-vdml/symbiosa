<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, array{question: string, answer: string}> $faq
 */
class Event extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'faq' => 'array',
    ];
}

<?php

namespace App\Traits;

use App\Models\Seo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSEO
{
    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'model');
    }
}

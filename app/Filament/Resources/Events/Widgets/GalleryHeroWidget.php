<?php

namespace App\Filament\Resources\Events\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class GalleryHeroWidget extends Widget
{
    protected string $view = 'filament.resources.events.widgets.gallery-hero-widget';

    protected int | string | array $columnSpan = 'full';

    public ?Model $record = null;
}

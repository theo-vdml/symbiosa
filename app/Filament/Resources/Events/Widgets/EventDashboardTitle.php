<?php

namespace App\Filament\Resources\Events\Widgets;

use BackedEnum;
use Filament\Widgets\Widget;


class EventDashboardTitle extends Widget
{
    protected string $view = 'filament.resources.events.widgets.event-dashboard-title';

    // Force le widget à prendre toute la largeur disponible
    protected int | string | array $columnSpan = 'full';

    // Optionnel : passer un titre dynamiquement
    public string $title = '';
    public BackedEnum|string|null $icon = null; // <-- Ajout de la propriété icône
}

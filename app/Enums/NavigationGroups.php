<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroups implements HasLabel
{
    case Pages;
    case News;
    case Events;
    case Ticketing;
    case Admin;

    public function getLabel(): string
    {
        return match ($this) {
            self::Pages => 'Pages',
            self::News => 'Actualités',
            self::Events => 'Gestion des événements',
            self::Ticketing => 'Billetterie',
            self::Admin => 'Administration',
        };
    }
}

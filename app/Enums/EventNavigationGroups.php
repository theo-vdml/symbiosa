<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EventNavigationGroups implements HasLabel
{
    case General;
    case Copywriting;
    case Ticketing;
    case Partners;


    public function getLabel(): string
    {
        return match ($this) {
            self::General => 'Général',
            self::Copywriting => 'Rédaction',
            self::Ticketing => 'Billetterie',
            self::Partners => 'Partenaires',
        };
    }
} {
    //
}

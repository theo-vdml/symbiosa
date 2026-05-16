<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum CheckoutStatus: string implements HasLabel, HasColor, HasIcon
{
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case PROCESSING = 'processing';
    case PENDING = 'pending';
    case EXPIRED = 'expired';

    public function getLabel(): string
    {
        return match ($this) {
            self::COMPLETED => 'Terminé',
            self::CANCELLED => 'Annulé',
            self::PROCESSING => 'En cours de traitement',
            self::PENDING => 'En attente',
            self::EXPIRED => 'Expiré',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::COMPLETED => 'success',
            self::CANCELLED => 'warning',
            self::PROCESSING => 'violet',
            self::PENDING => 'info',
            self::EXPIRED => 'danger',
        };
    }

    public function getIcon(): string | BackedEnum
    {
        return match ($this) {
            self::COMPLETED => Heroicon::Check,
            self::CANCELLED => Heroicon::XMark,
            self::PROCESSING => Heroicon::ArrowPath,
            self::PENDING => Heroicon::Clock,
            self::EXPIRED => Heroicon::CalendarDays,
        };
    }
}

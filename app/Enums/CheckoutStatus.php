<?php

namespace App\Enums;

enum CheckoutStatus: string
{
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case PROCESSING = 'processing';
    case PENDING = 'pending';
    case EXPIRED = 'expired';
    case STUCK = 'stuck';

    public function label(): string
    {
        return match ($this) {
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::PROCESSING => 'Processing',
            self::PENDING => 'Pending',
            self::EXPIRED => 'Expired',
            self::STUCK => 'Stuck in processing',
        };
    }
}

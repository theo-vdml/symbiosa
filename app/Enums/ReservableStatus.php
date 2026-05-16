<?php

namespace App\Enums;

enum ReservableStatus: string
{
    case UPCOMING = 'upcoming';
    case OPEN = 'open';
    case SOLD_OUT = 'sold_out';
}

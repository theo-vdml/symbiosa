<?php

namespace App\Filament\Resources\IssuedTickets\Pages;

use App\Filament\Resources\IssuedTickets\IssuedTicketResource;
use Filament\Resources\Pages\ListRecords;

class ListIssuedTickets extends ListRecords
{
    protected static string $resource = IssuedTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

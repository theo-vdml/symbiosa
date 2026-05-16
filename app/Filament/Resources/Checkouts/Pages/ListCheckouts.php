<?php

namespace App\Filament\Resources\Checkouts\Pages;

use App\Filament\Resources\Checkouts\CheckoutResource;
use Filament\Resources\Pages\ListRecords;

class ListCheckouts extends ListRecords
{
    protected static string $resource = CheckoutResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

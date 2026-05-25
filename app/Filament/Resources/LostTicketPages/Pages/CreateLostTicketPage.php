<?php

namespace App\Filament\Resources\LostTicketPages\Pages;

use App\Filament\Resources\LostTicketPages\LostTicketPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLostTicketPage extends CreateRecord
{
    protected static string $resource = LostTicketPageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}

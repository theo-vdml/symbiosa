<?php

namespace App\Filament\Resources\LostTicketPages\Pages;

use App\Filament\Resources\LostTicketPages\LostTicketPageResource;
use Filament\Resources\Pages\EditRecord;

class EditLostTicketPage extends EditRecord
{
    protected static string $resource = LostTicketPageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}

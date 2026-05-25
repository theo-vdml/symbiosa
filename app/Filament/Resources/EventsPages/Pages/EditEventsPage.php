<?php

namespace App\Filament\Resources\EventsPages\Pages;

use App\Filament\Resources\EventsPages\EventsPageResource;
use Filament\Resources\Pages\EditRecord;

class EditEventsPage extends EditRecord
{
    protected static string $resource = EventsPageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}

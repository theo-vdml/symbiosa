<?php

namespace App\Filament\Resources\ArchivePages\Pages;

use App\Filament\Resources\ArchivePages\ArchivePageResource;
use Filament\Resources\Pages\EditRecord;

class EditArchivePage extends EditRecord
{
    protected static string $resource = ArchivePageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}

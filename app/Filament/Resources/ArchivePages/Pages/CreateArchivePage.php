<?php

namespace App\Filament\Resources\ArchivePages\Pages;

use App\Filament\Resources\ArchivePages\ArchivePageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArchivePage extends CreateRecord
{
    protected static string $resource = ArchivePageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}

<?php

namespace App\Filament\Resources\NewsPages\Pages;

use App\Filament\Resources\NewsPages\NewsPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsPage extends CreateRecord
{
    protected static string $resource = NewsPageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}

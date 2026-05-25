<?php

namespace App\Filament\Resources\ContactPages\Pages;

use App\Filament\Resources\ContactPages\ContactPageResource;
use App\Models\ContactPage;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContactPages extends ListRecords
{
    protected static string $resource = ContactPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        $record = ContactPage::first();

        if ($record) {
            redirect(static::$resource::getUrl('edit', ['record' => $record]));
            return;
        }

        redirect(static::$resource::getUrl('create'));
    }
}

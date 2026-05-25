<?php

namespace App\Filament\Resources\AboutPages\Pages;

use App\Filament\Resources\AboutPages\AboutPageResource;
use App\Models\AboutPage;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAboutPages extends ListRecords
{
    protected static string $resource = AboutPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        $record = AboutPage::first();

        if ($record) {
            redirect(static::$resource::getUrl('edit', ['record' => $record]));
            return;
        }

        redirect(static::$resource::getUrl('create'));
    }
}

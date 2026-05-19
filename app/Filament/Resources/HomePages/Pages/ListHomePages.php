<?php

namespace App\Filament\Resources\HomePages\Pages;

use App\Filament\Resources\HomePages\HomePageResource;
use App\Models\HomePage;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomePages extends ListRecords
{
    protected static string $resource = HomePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        $record = HomePage::first();

        if ($record) {
            redirect(static::$resource::getUrl('edit', ['record' => $record]));
            return;
        }

        redirect(static::$resource::getUrl('create'));
    }
}

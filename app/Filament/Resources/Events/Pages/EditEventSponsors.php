<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use BackedEnum;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EditEventSponsors extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Sponsors';

    protected static ?string $breadcrumb = 'Sponsors';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Sponsors';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Heart;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getSponsorsSchema());
    }
}

<?php

namespace App\Filament\Resources\Events\Pages;

use App\Enums\EventNavigationGroups;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use BackedEnum;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class EditEventVisuals extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Identité visuelle';

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::General;

    protected static ?string $breadcrumb = 'Identité visuelle';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Identité visuelle';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getVisualsSchema());
    }
}

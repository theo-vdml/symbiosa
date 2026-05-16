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

class EditEventCopywritting extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::Copywriting;

    protected static ?string $navigationLabel = 'Contenu de la page';

    protected static ?string $breadcrumb = 'Contenu de la page';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Contenu de la page';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getCopywrittingSchema());
    }
}

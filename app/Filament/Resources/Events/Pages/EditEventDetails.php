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

class EditEventDetails extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Informations pratiques';

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::General;

    protected static ?string $breadcrumb = 'Informations pratiques';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Informations pratiques';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...EventForm::getDateTimeSchema(),
                ...EventForm::getLocationSchema(),
                ...EventForm::getDetailsSchema()
            ]);
    }
}

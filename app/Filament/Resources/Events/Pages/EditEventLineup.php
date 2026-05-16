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

class EditEventLineup extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::General;

    protected static ?string $navigationLabel = 'Lineup';

    protected static ?string $breadcrumb = 'Lineup';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Lineup';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getLineupSchema());
    }
}

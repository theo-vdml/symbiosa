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

class EditEventArchives extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::General;

    protected static ?string $navigationLabel = 'Archives';

    protected static ?string $breadcrumb = 'Archives';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Archives';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getArchivesSchema());
    }
}

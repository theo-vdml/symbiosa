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

class EditEventSeo extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'SEO';

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::General;

    protected static ?string $breadcrumb = 'SEO';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - SEO';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getSeoSchema());
    }
}

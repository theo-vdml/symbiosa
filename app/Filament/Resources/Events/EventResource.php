<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\EditEventAddons;
use App\Filament\Resources\Events\Pages\EditEventCopywritting;
use App\Filament\Resources\Events\Pages\EditEventDetails;
use App\Filament\Resources\Events\Pages\EditEventFaq;
use App\Filament\Resources\Events\Pages\EditEventLineup;
use App\Filament\Resources\Events\Pages\EditEventPublication;
use App\Filament\Resources\Events\Pages\EditEventSponsors;
use App\Filament\Resources\Events\Pages\EditEventTicketing;
use App\Filament\Resources\Events\Pages\EditEventVisuals;
use App\Filament\Resources\Events\Pages\ListEventCheckouts;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\Schemas\EventForm;
use App\Filament\Resources\Events\Schemas\EventInfolist;
use App\Filament\Resources\Events\Tables\EventsTable;
use App\Models\Event;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 0;

    protected static string|UnitEnum|null $navigationGroup = 'Gestion des événements';
    protected static string|null $modelLabel = 'Événement';
    protected static string|null $pluralModelLabel = 'Événements';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function form(Schema $schema): Schema
    {
        return EventForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
            'publication' => EditEventPublication::route('/{record}/publication'),
            'details' => EditEventDetails::route('/{record}/details'),
            'copywritting' => EditEventCopywritting::route('/{record}/copywritting'),
            'visuals' => EditEventVisuals::route('/{record}/visuals'),
            'faq' => EditEventFaq::route('/{record}/faq'),
            'sponsors' => EditEventSponsors::route('/{record}/sponsors'),
            'lineup' => EditEventLineup::route('/{record}/lineup'),
            'ticketing' => EditEventTicketing::route('/{record}/ticketing'),
            'addons' => EditEventAddons::route('/{record}/addons'),
            'checkouts' => ListEventCheckouts::route('/{record}/checkouts'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditEvent::class,
            EditEventPublication::class,
            EditEventDetails::class,
            EditEventCopywritting::class,
            EditEventVisuals::class,
            EditEventLineup::class,
            EditEventFaq::class,
            EditEventSponsors::class,
            EditEventTicketing::class,
            EditEventAddons::class,
            ListEventCheckouts::class,
        ]);
    }
}

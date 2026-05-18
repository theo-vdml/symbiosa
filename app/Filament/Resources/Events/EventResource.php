<?php

namespace App\Filament\Resources\Events;

use App\Enums\NavigationGroups;
use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\EditEventAddons;
use App\Filament\Resources\Events\Pages\EditEventArchives;
use App\Filament\Resources\Events\Pages\EditEventCopywritting;
use App\Filament\Resources\Events\Pages\EditEventDetails;
use App\Filament\Resources\Events\Pages\EditEventFaq;
use App\Filament\Resources\Events\Pages\EditEventLineup;
use App\Filament\Resources\Events\Pages\EditEventSponsors;
use App\Filament\Resources\Events\Pages\EditEventSeo;
use App\Filament\Resources\Events\Pages\EditEventTicketing;
use App\Filament\Resources\Events\Pages\EditEventTicketingConfig;
use App\Filament\Resources\Events\Pages\EditEventVisuals;
use App\Filament\Resources\Events\Pages\ListEventCheckinLists;
use App\Filament\Resources\Events\Pages\ListEventCheckouts;
use App\Filament\Resources\Events\Pages\ListEventIssuedTickets;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\Pages\EventDashboard;
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

    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::Events;
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
            'dashboard' => EventDashboard::route('/{record}'),
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
            'details' => EditEventDetails::route('/{record}/details'),
            'copywritting' => EditEventCopywritting::route('/{record}/copywritting'),
            'visuals' => EditEventVisuals::route('/{record}/visuals'),
            'archives' => EditEventArchives::route('/{record}/archives'),
            'seo' => EditEventSeo::route('/{record}/seo'),
            'faq' => EditEventFaq::route('/{record}/faq'),
            'sponsors' => EditEventSponsors::route('/{record}/sponsors'),
            'lineup' => EditEventLineup::route('/{record}/lineup'),
            'ticketing' => EditEventTicketing::route('/{record}/ticketing'),
            'ticketing-config' => EditEventTicketingConfig::route('/{record}/ticketing-config'),
            'addons' => EditEventAddons::route('/{record}/addons'),
            'checkouts' => ListEventCheckouts::route('/{record}/checkouts'),
            'issued-tickets' => ListEventIssuedTickets::route('/{record}/issued-tickets'),
            'checkin-lists' => ListEventCheckinLists::route('/{record}/checkin-lists'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EventDashboard::class,
            EditEvent::class,
            EditEventDetails::class,
            EditEventCopywritting::class,
            EditEventVisuals::class,
            EditEventArchives::class,
            EditEventSeo::class,
            EditEventLineup::class,
            EditEventFaq::class,
            EditEventSponsors::class,
            EditEventTicketingConfig::class,
            EditEventTicketing::class,
            EditEventAddons::class,
            ListEventCheckouts::class,
            ListEventIssuedTickets::class,
            ListEventCheckinLists::class,
        ]);
    }
}

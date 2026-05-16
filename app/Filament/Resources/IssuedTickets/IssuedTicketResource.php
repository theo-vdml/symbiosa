<?php

namespace App\Filament\Resources\IssuedTickets;

use App\Enums\NavigationGroups;
use App\Filament\Resources\IssuedTickets\Pages\ListIssuedTickets;
use App\Filament\Resources\IssuedTickets\Schemas\IssuedTicketInfolist;
use App\Filament\Resources\IssuedTickets\Tables\IssuedTicketTable;
use App\Models\IssuedTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IssuedTicketResource extends Resource
{
    protected static ?string $model = IssuedTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QrCode;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::Ticketing;

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $recordTitleAttribute = 'public_id';

    public static function infolist(Schema $schema): Schema
    {
        return IssuedTicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IssuedTicketTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIssuedTickets::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\IssuedTickets;

use App\Filament\Resources\IssuedTickets\Pages\ListIssuedTickets;
use App\Filament\Resources\IssuedTickets\Schemas\IssuedTicketInfolist;
use App\Filament\Resources\IssuedTickets\Tables\IssuedTicketTable;
use App\Models\IssuedTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IssuedTicketResource extends Resource
{
    protected static ?string $model = IssuedTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'qr_code_token';

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

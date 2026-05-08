<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\IssuedTickets\Schemas\IssuedTicketInfolist;
use App\Filament\Resources\IssuedTickets\Tables\IssuedTicketTable;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ListEventIssuedTickets extends ManageRelatedRecords
{
    protected static string $resource = EventResource::class;

    protected static string $relationship = 'issuedTickets';

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $breadcrumb = 'Tickets';

    protected static string|UnitEnum|null $navigationGroup = 'Billetterie';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Tickets';
    }

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    public function table(Table $table): Table
    {
        return IssuedTicketTable::configure($table, false);
    }

    public function infolist(Schema $schema): Schema
    {
        return IssuedTicketInfolist::configure($schema);
    }
}

<?php

namespace App\Filament\Resources\Events\Pages;

use App\Enums\EventNavigationGroups;
use App\Filament\Resources\Checkouts\Schemas\CheckoutInfolist;
use App\Filament\Resources\Checkouts\Tables\CheckoutsTable;
use App\Filament\Resources\Events\EventResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ListEventCheckouts extends ManageRelatedRecords
{
    protected static string $resource = EventResource::class;

    protected static string $relationship = 'checkouts';

    protected static ?string $navigationLabel = 'Commandes';

    protected static ?string $breadcrumb = 'Commandes';

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::Ticketing;

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Commandes';
    }

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    public function table(Table $table): Table
    {
        return CheckoutsTable::configure($table, false);
    }

    public function infolist(Schema $schema): Schema
    {
        return CheckoutInfolist::configure($schema);
    }
}

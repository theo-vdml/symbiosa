<?php

namespace App\Filament\Resources\Checkouts;

use App\Filament\Resources\Checkouts\Pages\ListCheckouts;
use App\Filament\Resources\Checkouts\Schemas\CheckoutInfolist;
use App\Filament\Resources\Checkouts\Tables\CheckoutsTable;
use App\Models\Checkout;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CheckoutResource extends Resource
{
    protected static ?string $model = Checkout::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|\UnitEnum|null $navigationGroup = 'Billetterie';

    protected static string|null $modelLabel = 'Commande';
    protected static string|null $pluralModelLabel = 'Commandes';

    protected static ?string $recordTitleAttribute = 'uuid';

    public static function infolist(Schema $schema): Schema
    {
        return CheckoutInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CheckoutsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCheckouts::route('/'),
        ];
    }
}

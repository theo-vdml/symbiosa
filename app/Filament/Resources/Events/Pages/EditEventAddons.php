<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class EditEventAddons extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Extras';
    protected static string|UnitEnum|null $navigationGroup = 'Billetterie';

    protected static ?string $breadcrumb = 'Extras';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Extras';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Extras')
                    ->description('Gérez les différents extras disponibles pour cet événement, leurs prix, capacités et disponibilités.')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('addons')
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['name']) ? $state['name'] : 'Nouvel extra'
                            )
                            ->addActionLabel('Ajouter un extra')
                            ->schema([
                                Grid::make(6)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nom de l\'extra')
                                            ->placeholder('Ex: Extra VIP, Cadeau, etc.')
                                            ->prefixIcon(Heroicon::OutlinedGift)
                                            ->required()
                                            ->columnSpan(4)
                                            ->live(true),

                                        TextInput::make('price')
                                            ->label('Prix (€)')
                                            ->numeric()
                                            ->required()
                                            ->prefix('€')
                                            ->formatStateUsing(fn($state) => $state / 100)
                                            ->dehydrateStateUsing(fn($state) => $state * 100)
                                            ->columnSpan(2)
                                            ->minValue(0)
                                            ->live(true),

                                        Textarea::make('description')
                                            ->label('Description de l\'extra')
                                            ->placeholder('Détails supplémentaires sur ce type d\'extra')
                                            ->autosize()
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        DateTimePicker::make('available_from')
                                            ->label('Disponible à partir de')
                                            ->placeholder('Date et heure à partir de laquelle cet extra sera disponible à la vente')
                                            ->prefixIcon(Heroicon::OutlinedCalendarDays)
                                            ->native(false)
                                            ->displayFormat('d/m/Y H:i')
                                            ->seconds(false)
                                            ->columnSpan(3),

                                        DateTimePicker::make('available_until')
                                            ->label('Disponible jusqu\'à')
                                            ->placeholder('Date et heure jusqu\'à laquelle cet extra sera disponible à la vente')
                                            ->prefixIcon(Heroicon::OutlinedCalendarDays)
                                            ->native(false)
                                            ->displayFormat('d/m/Y H:i')
                                            ->seconds(false)
                                            ->columnSpan(3),

                                        TextInput::make('capacity')
                                            ->label('Capacité totale')
                                            ->placeholder('Nombre total d\'extras disponibles pour ce type')
                                            ->prefixIcon(Heroicon::Square3Stack3d)
                                            ->numeric()
                                            ->integer()
                                            ->minValue(1)
                                            ->columnSpan(3),

                                        TextInput::make('max_per_order')
                                            ->label('Qtt max / commande')
                                            ->placeholder('Nombre maximum d\'extras par commande')
                                            ->prefixIcon(Heroicon::OutlinedShoppingCart)
                                            ->numeric()
                                            ->integer()
                                            ->minValue(1)
                                            ->columnSpan(3),
                                    ]),
                            ])
                    ]),

            ]);
    }
}

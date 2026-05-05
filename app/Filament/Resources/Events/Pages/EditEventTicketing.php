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
use YousefAman\ModalRepeater\Column;
use YousefAman\ModalRepeater\ModalRepeater;

class EditEventTicketing extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Prix des billets';

    protected static string|UnitEnum|null $navigationGroup = 'Billetterie';

    protected static ?string $breadcrumb = 'Prix des billets';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Prix des billets';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Types de billets')
                    ->description('Gérez les différents types de billets disponibles pour cet événement, leurs prix, capacités et disponibilités.')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('ticketTypes')
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['name']) ? $state['name'] : 'Nouveau type de billet'
                            )
                            ->addActionLabel('Ajouter un type de billet')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nom du billet')
                                            ->placeholder('Ex: Billet standard, VIP, etc.')
                                            ->prefixIcon(Heroicon::OutlinedTicket)
                                            ->required()
                                            ->columnSpan(2)
                                            ->live(true),

                                        TextInput::make('capacity')
                                            ->label('Capacité totale')
                                            ->placeholder('Nombre total de billets disponibles pour ce type')
                                            ->prefixIcon(Heroicon::Square3Stack3d)
                                            ->numeric()
                                            ->integer()
                                            ->minValue(1)
                                            ->required()
                                            ->columnSpan(1),

                                        Textarea::make('description')
                                            ->label('Description du billet')
                                            ->placeholder('Détails supplémentaires sur ce type de billet')
                                            ->autosize()
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        DateTimePicker::make('available_from')
                                            ->label('Disponible à partir de')
                                            ->placeholder('Date et heure à partir de laquelle ce billet sera disponible à la vente')
                                            ->prefixIcon(Heroicon::OutlinedCalendarDays)
                                            ->native(false)
                                            ->displayFormat('d/m/Y H:i')
                                            ->seconds(false)
                                            ->columnSpan(2),

                                        TextInput::make('max_per_order')
                                            ->label('Qtt max / commande')
                                            ->placeholder('Nombre maximum de billets par commande')
                                            ->prefixIcon(Heroicon::OutlinedShoppingCart)
                                            ->numeric()
                                            ->integer()
                                            ->minValue(1)
                                            ->columnSpan(1),
                                    ]),
                                ModalRepeater::make('prices')
                                    ->relationship() // Cherche la relation 'prices' dans le modèle TicketType
                                    ->label('Phases de tarification')
                                    ->emptyLabel('Ajouter une phase de tarification')
                                    ->orderColumn('sort_order')
                                    ->reorderableWithButtons(false)
                                    ->tableColumns([
                                        Column::make('name')
                                            ->label('Phase'),
                                        Column::make('price_in_euro')
                                            ->label('Prix')
                                            ->formatUsing(
                                                fn($state) =>
                                                number_format($state, 2, ',', ' ') . ' €'
                                            ),
                                        Column::make('threshold')
                                            ->label('Seuil'),
                                        Column::make('available_until')
                                            ->label('Fin')
                                            ->formatUsing(
                                                fn($state) =>
                                                $state ? date('d/m/Y H:i', strtotime($state)) : null
                                            ),
                                    ])
                                    ->addActionLabel('Ajouter un prix')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nom de la phase')
                                            ->placeholder('Ex: Early Bird')
                                            ->required()
                                            ->columnSpan(1)
                                            ->live(true),

                                        TextInput::make('price_in_euro')
                                            ->label('Prix')
                                            ->numeric()
                                            ->required()
                                            ->prefix('€')
                                            ->columnSpan(1)
                                            ->minValue(0)
                                            ->live(true),

                                        TextInput::make('threshold')
                                            ->label('Seuil (Ventes cumulées)')
                                            ->placeholder('Jusqu\'au Xème billet')
                                            ->numeric()
                                            ->integer()
                                            ->helperText('La phase s\'arrête quand ce total est atteint.')
                                            ->columnSpan(1),

                                        DateTimePicker::make('available_until')
                                            ->label('Fin de validité (Date)')
                                            ->prefixIcon(Heroicon::OutlinedCalendarDays)
                                            ->native(false)
                                            ->displayFormat('d/m/Y H:i')
                                            ->seconds(false)
                                            ->helperText('La phase s\'arrête à cette date.')
                                            ->columnSpan(1),
                                    ])
                                    ->defaultItems(0)
                                    ->minItems(1)
                            ])
                    ]),

            ]);
    }
}

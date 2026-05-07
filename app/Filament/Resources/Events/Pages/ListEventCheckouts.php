<?php

namespace App\Filament\Resources\Events\Pages\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Actions\CopyAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ListEventCheckouts extends ManageRelatedRecords
{
    protected static string $resource = EventResource::class;

    protected static string $relationship = 'checkouts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|UnitEnum|null $navigationGroup = "Billetterie";

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                TextInput::make('event_id')
                    ->required()
                    ->numeric(),
                TextInput::make('customer_email')
                    ->email(),
                TextInput::make('customer_name'),
                TextInput::make('stripe_session_id'),
                DateTimePicker::make('expires_at')
                    ->required(),
                DateTimePicker::make('completed_at'),
                DateTimePicker::make('cancelled_at'),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    // --- SECTION 1 : TRANSACTION ---
                    Section::make('Détails de la transaction')
                        ->icon('heroicon-m-credit-card')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('status')
                                ->badge(),
                            TextEntry::make('uuid')
                                ->label('Identifiant')
                                ->suffixAction(
                                    CopyAction::make()
                                        ->icon(Heroicon::Clipboard)
                                )
                                ->fontFamily('mono')
                                ->size('xs'),
                            TextEntry::make('created_at')
                                ->label('Créé le')
                                ->dateTime(),
                            TextEntry::make('completed_at')
                                ->label('Payé le')
                                ->dateTime()
                                ->placeholder('En attente'),
                            TextEntry::make('expires_at')
                                ->label('Expire le')
                                ->dateTime(),
                            TextEntry::make('stripe_session_id')
                                ->label('ID Stripe')
                                ->fontFamily('mono')
                                ->suffixAction(
                                    CopyAction::make()
                                        ->icon(Heroicon::Clipboard)
                                )
                                ->size('xs')
                                ->placeholder('N/A'),
                        ]),

                    // --- SECTION 2 : CLIENT ---
                    Section::make('Informations Client')
                        ->icon('heroicon-m-user')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('customer_name')
                                ->label('Nom')
                                ->placeholder('Non renseigné'),
                            TextEntry::make('customer_email')
                                ->label('Email')
                                ->placeholder('Non renseigné')
                                ->suffixAction(
                                    CopyAction::make()
                                        ->icon(Heroicon::Clipboard)
                                ),
                        ]),

                    // --- SECTION 3 : PANIER (Redessiné) ---
                    Section::make('Détail du panier')
                        ->icon('heroicon-m-shopping-bag')
                        ->schema([
                            RepeatableEntry::make('reservations')
                                ->hiddenLabel()
                                ->schema([
                                    Grid::make(4)
                                        ->schema([
                                            TextEntry::make('reservable_summary')
                                                ->label(function ($record) {
                                                    return match ($record->reservable_type) {
                                                        'App\Models\TicketType' => 'Ticket',
                                                        'App\Models\EventAddon' => 'Addon',
                                                        default => $record->reservable_type,
                                                    };
                                                })
                                                ->state(function ($record) {
                                                    // $record est ici une instance de Reservation
                                                    $reservable = $record->reservable;

                                                    if ($record->reservable_type === 'App\Models\TicketType') {
                                                        // On vérifie si la relation ou la donnée du prix existe sur la réservation
                                                        // Note: Adapte 'ticket_price' au nom réel de ta relation/colonne sur Reservation
                                                        $priceName = $record->ticketPrice?->name ?? 'Tarif standard';
                                                        return "{$reservable->name} - {$priceName}";
                                                    }

                                                    return $reservable->name;
                                                })
                                                ->weight(FontWeight::Bold)
                                                ->columnSpan(2),
                                            TextEntry::make('quantity')

                                                ->formatStateUsing(fn($state) => "x{$state}")
                                                ->color('gray')
                                                ->columnSpan(1),
                                            TextEntry::make('unit_price')
                                                ->state(fn($record) => ($record->quantity * $record->unit_price) / 100)
                                                ->money('eur')
                                                ->weight(FontWeight::Bold)
                                                ->columnSpan(1),
                                        ]),
                                ])
                                ->grid(1),
                            Group::make()
                                ->extraAttributes(['class' => 'mt-4 pt-4 border-t border-gray-200 dark:border-white/10'])
                                ->schema([
                                    TextEntry::make('total_amount')
                                        ->label("Total")
                                        ->state(fn($record) => $record->reservations->sum(fn($r) => $r->quantity * $r->unit_price) / 100)
                                        ->money('eur')
                                        ->weight(FontWeight::Bold)
                                        ->size('lg')
                                ]),
                        ]),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('uuid')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Client')
                    ->placeholder('En attente des infos...')
                    ->description(function ($record) {
                        return $record->customer_email;
                    })
                    ->searchable(['customer_name', 'customer_email']),

                // Utilisation de l'accessor "status" défini dans ton modèle
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),

                TextColumn::make('expires_at')
                    ->label('Expiration')
                    ->formatStateUsing(fn($record) => $record->expires_at->diffForHumans())
                    ->sortable(),

                TextColumn::make('stripe_session_id')
                    ->label('Stripe ID')
                    ->copyable()
                    ->toggledHiddenByDefault()
                    ->fontFamily('mono')
                    ->limit(10),

                TextColumn::make('completed_at')
                    ->label('Payé le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Non finalisé')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'isCompleted' => 'Complétés',
                        'isPending' => 'En attente (actifs)',
                        'isExpired' => 'Expirés',
                        'isCancelled' => 'Annulés',
                    ])
                    // Ici on utilise tes Scopes Eloquent pour filtrer
                    ->query(function ($query, array $data) {
                        return match ($data['value']) {
                            'isCompleted' => $query->isCompleted(),
                            'isPending' => $query->isPending(),
                            'isExpired' => $query->isExpired(),
                            'isCancelled' => $query->isCancelled(),
                            default => $query,
                        };
                    })
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Détails')
                    ->modalHeading('Détails du Checkout')
                    ->slideOver(),
            ])
            ->headerActions([]);
    }
}

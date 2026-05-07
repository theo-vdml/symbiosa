<?php

namespace App\Filament\Resources\Checkouts\Schemas;

use Filament\Forms\Components\TextInput\Actions\CopyAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

class CheckoutInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
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

                    Section::make('Contenu du panier')
                        ->icon('heroicon-m-shopping-bag')
                        ->schema([
                            RepeatableEntry::make('reservations')
                                ->hiddenLabel()
                                ->schema([
                                    Grid::make(4)
                                        ->schema([
                                            TextEntry::make('reservable_summary')
                                                ->hiddenLabel()
                                                ->state(function ($record) {
                                                    $reservable = $record->reservable;
                                                    if ($record->reservable_type === 'App\Models\TicketType') {
                                                        $priceName = $record->ticketPrice?->name ?? 'Tarif standard';
                                                        return "{$reservable->name} - {$priceName}";
                                                    }

                                                    return $reservable->name;
                                                })
                                                ->weight(FontWeight::Bold)
                                                ->columnSpan(2),
                                            TextEntry::make('quantity')
                                                ->label('Quantité')
                                                ->formatStateUsing(fn($state) => "x{$state}")
                                                ->color('gray')
                                                ->columnSpan(1),
                                            TextEntry::make('total_price')
                                                ->label('Total')
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
                    Section::make('Billets générés')
                        ->icon('heroicon-m-ticket')
                        ->schema([
                            RepeatableEntry::make('issuedTickets')
                                ->hiddenLabel()
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            TextEntry::make('qr_code_token')
                                                ->label(function ($record) {
                                                    $reservable = $record->reservable;
                                                    return $reservable->name;
                                                })
                                                ->copyable()
                                                ->weight(FontWeight::Bold)
                                                ->columnSpan(2),
                                            TextEntry::make('status')
                                                ->label("Statut")
                                                ->badge()
                                                ->state(function ($record) {
                                                    return $record->scanned_at ? 'Utilisé' : 'Non utilisé';
                                                })
                                                ->color(function ($record) {
                                                    return $record->scanned_at ? 'success' : 'info';
                                                })
                                                ->placeholder('Pas encore scanné')
                                                ->columnSpan(1),
                                        ]),
                                ])
                                ->grid(1)
                                ->placeholder('Aucun billet généré pour cette session.'),
                        ])

                ])
                    ->columnSpanFull(),
            ]);
    }
}

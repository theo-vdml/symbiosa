<?php

namespace App\Filament\Resources\IssuedTickets\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class IssuedTicketTable
{
    public static function configure(Table $table, bool $withEvent = true): Table
    {
        return $table
            ->columns([
                TextColumn::make('public_id')
                    ->label('ID du Ticket')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('event.title')
                    ->label('Événement')
                    ->description(function ($record) {
                        return $record->event->date->format('d/m/Y');
                    })
                    ->searchable()
                    ->sortable(['events.start_at'])
                    ->hidden(!$withEvent),

                TextColumn::make('type')
                    ->label('Type')
                    ->state(function ($record) {
                        return $record->reservable_type === 'App\Models\TicketType' ? 'Ticket' : 'Addon';
                    })
                    ->color(function ($record) {
                        return $record->reservable_type === 'App\Models\TicketType' ? 'primary' : 'info';
                    })
                    ->badge()
                    ->sortable(['reservable_type']),

                TextColumn::make('product')
                    ->label('Nom')
                    ->state(function ($record) {
                        return $record->reservable->name  . ($record->ticketPrice ? ' - ' . $record->ticketPrice->name : '');
                    })
                    ->placeholder('N/A'),

                TextColumn::make('price_paid')
                    ->label('Prix Payé')
                    ->money('EUR', divideBy: 100)
                    ->sortable(),

                TextColumn::make('checkout.uuid')
                    ->label('Commande liée')
                    ->limit(10)
                    ->fontFamily('mono')
                    ->toggleable(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\IssuedTickets\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class IssuedTicketTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Identifying the Ticket Holder
                TextColumn::make('name')
                    ->label('Attendee Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => "Ref: {$record->qr_code_token}"),

                // Displaying the polymorphic 'Reservable' relation (e.g., Event or Workshop name)
                TextColumn::make('reservable.name')
                    ->label('Resource')
                    ->placeholder('N/A')
                    ->sortable(),

                // Financial Information
                TextColumn::make('price_paid')
                    ->money('USD', divideBy: 100) // Assuming storage in cents
                    ->sortable(),

                // Status & Attendance
                TextColumn::make('is_attendee')
                    ->label('Status')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Checked In' : 'Pending'),

                // Timestamp for Scanning
                TextColumn::make('scanned_at')
                    ->label('Entry Time')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Quick Link to Checkout
                TextColumn::make('checkout.id')
                    ->label('Order ID')
                    ->numeric()
                    ->toggleable(),
            ])
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_attendee')
                    ->label('Attendance Status'),
                \Filament\Tables\Filters\SelectFilter::make('reservable_type')
                    ->label('Type')
                    ->options([
                        'App\Models\Event' => 'Event',
                        'App\Models\Workshop' => 'Workshop',
                    ]),
            ]);
    }
}

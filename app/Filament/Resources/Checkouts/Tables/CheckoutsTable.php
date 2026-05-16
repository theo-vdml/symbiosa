<?php

namespace App\Filament\Resources\Checkouts\Tables;

use App\Enums\CheckoutStatus;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CheckoutsTable
{
    public static function configure(Table $table, bool $withEvent = true): Table
    {
        return $table
            ->recordTitleAttribute('uuid')
            ->columns([
                TextColumn::make('uuid')
                    ->label('Identifiant')
                    ->copyable()
                    ->fontFamily('mono')
                    ->limit(10)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('event.title')
                    ->hidden(!$withEvent)
                    ->label('Événement')
                    ->description(function ($record) {
                        return $record->event?->date?->format('d/m/Y');
                    })
                    ->toggleable()
                    ->searchable(['event.title']),

                TextColumn::make('customer_name')
                    ->label('Client')
                    ->default('—')
                    ->description(function ($record) {
                        return $record->customer_email;
                    })
                    ->searchable(['customer_name', 'customer_email'])
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->description(function ($record) {
                        if ($record->status === CheckoutStatus::COMPLETED) {
                            return $record->completed_at?->diffForHumans();
                        } elseif ($record->status === CheckoutStatus::EXPIRED) {
                            return $record->expires_at?->diffForHumans();
                        }
                    })
                    ->toggleable(),

                TextColumn::make('stripe_session_id')
                    ->label('Stripe ID')
                    ->copyable()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->fontFamily('mono')
                    ->limit(10)
                    ->searchable()
                    ->default('—'),
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
                    ->slideOver()
                    ->label('Détails')
                    ->color('primary')
                    ->icon(Heroicon::Eye)
            ])
            ->headerActions([]);
    }
}

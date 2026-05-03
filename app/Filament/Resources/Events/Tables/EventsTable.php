<?php

namespace App\Filament\Resources\Events\Tables;

use App\Enums\PublicationStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use TinusG\FilamentHoverImageColumn\HoverImageColumn;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                HoverImageColumn::make('poster')
                    ->disk('public')
                    ->label('Affiche')
                    ->square(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn($record) => $record->status->getDynamicLabel($record->published_at))
                    ->color(fn($record) => $record->status->getDynamicColor($record->published_at))
                    ->icon(fn($record) => $record->status->getDynamicIcon($record->published_at))
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date de l\'event')
                    ->date()
                    ->sortable(),

                TextColumn::make('city')
                    ->label('Ville')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PublicationStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

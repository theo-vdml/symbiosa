<?php

namespace App\Filament\Resources\Artists\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArtistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('portrait')
                    ->label('Portrait')
                    ->collection('portrait')
                    ->circular(),

                TextColumn::make('name')
                    ->label('Nom de l\'artiste')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('website')
                    ->label('Site web')
                    ->openUrlInNewTab()
                    ->sortable()
                    ->searchable()
                    ->default('—'),

                TextColumn::make('genres.name')
                    ->label('Genres')
                    ->sortable()
                    ->searchable()
                    ->badge(),
            ])
            ->filters([
                //
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

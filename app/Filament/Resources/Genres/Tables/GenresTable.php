<?php

namespace App\Filament\Resources\Genres\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GenresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom du Genre')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Identifiant (Slug)')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Modifier le genre')
                    ->modalDescription('Ajustez les détails de votre genre ci-dessous.')
                    ->modalSubmitActionLabel('Enregistrer les changements')
                    ->label('Modifier')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->color('primary'),
                DeleteAction::make()
                    ->modalHeading('Confirmer la suppression')
                    ->modalDescription('Êtes-vous sûr de vouloir supprimer ce genre ? Cette action est irréversible.')
                    ->modalSubmitActionLabel('Supprimer le genre')
                    ->label('Supprimer')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

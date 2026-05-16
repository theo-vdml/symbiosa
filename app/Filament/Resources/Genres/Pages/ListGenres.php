<?php

namespace App\Filament\Resources\Genres\Pages;

use App\Filament\Resources\Genres\GenreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGenres extends ListRecords
{
    protected static string $resource = GenreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajouter un genre')
                ->modalHeading('Créer un nouveau genre')
                ->modalDescription('Remplissez les détails de votre nouveau genre ci-dessous.')
                ->modalSubmitActionLabel('Créer le genre')
                ->createAnother(false),
        ];
    }
}

<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajouter une catégorie')
                ->modalHeading('Créer une nouvelle catégorie')
                ->modalDescription('Remplissez les détails de votre nouvelle catégorie ci-dessous.')
                ->modalSubmitActionLabel('Créer la catégorie')
                ->createAnother(false),
        ];
    }
}

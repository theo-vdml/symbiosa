<?php

namespace App\Filament\Resources\Artists\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArtistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make("Informations de l'artiste")
                            ->description('Remplissez les informations de base de l\'artiste.')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nom de l\'artiste')
                                    ->required(),

                                TextInput::make('website')
                                    ->label('Site web de l\'artiste')
                                    ->url()
                                    ->nullable(),

                                Select::make('genres')
                                    ->label('Genres musicaux')
                                    ->multiple()
                                    ->relationship('genres', 'name')
                                    ->preload()
                                    ->quickAdd(label: "Nouveau genre: {search}", resetSearch: true)
                                    ->searchable(),
                            ]),

                        Section::make("Biographie de l'artiste")
                            ->description('Ajoutez une biographie pour présenter l\'artiste.')
                            ->schema([
                                Textarea::make('biography')
                                    ->label('Biographie')
                                    ->placeholder('Entrez une biographie pour l\'artiste...')
                                    ->autosize()
                                    ->rows(5)
                                    ->nullable(),
                            ]),
                    ]),

                Section::make("Visuels de l'artiste")
                    ->description('Ajoutez une photo de l\'artiste pour une meilleure présentation.')
                    ->schema([

                        SpatieMediaLibraryFileUpload::make('portrait')
                            ->label('Portrait de l\'artiste')
                            ->collection('portrait')
                            ->disk('r2')
                            ->visibility('public')
                            ->image()
                            ->automaticallyResizeImagesMode('cover')
                            ->automaticallyResizeImagesToWidth('1080')
                            ->nullable()
                            ->optimize('webp', 85),

                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Genres\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class GenreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom du genre')
                    ->prefixIcon(Heroicon::MusicalNote)
                    ->placeholder('Ex : Techno, Drum & Bass, House.')
                    ->belowContent('Le nom du genre est ce qui sera affiché aux utilisateurs. Choisissez un nom clair et descriptif pour faciliter la navigation et la compréhension du contenu associé à ce genre.')
                    ->columnSpanFull()
                    ->required(),

                TextInput::make('slug')
                    ->label('Identifiant (Slug)')
                    ->prefixIcon(Heroicon::Link)
                    ->placeholder('ex: techno-drum-bass')
                    ->belowContent('Le slug est une version "URL friendly" du nom du genre, généralement en minuscules et avec des tirets à la place des espaces.')
                    ->suffixAction(
                        Action::make('generateSlug')
                            ->label('Genérer sur base du nom')
                            ->icon(Heroicon::Sparkles)
                            ->action(function (Set $set, Get $get) {
                                $name = $get('name');
                                if (blank($name)) return;
                                $set('slug', Str::slug($name));
                            })
                    )
                    ->alphaDash()
                    ->minLength(3)
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->required(),
            ]);
    }
}

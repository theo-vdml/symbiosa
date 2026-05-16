<?php

namespace App\Filament\Resources\LegalPages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Str;

class LegalPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(4)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Détails de la page')
                            ->columnSpan([
                                'default' => 4,
                                '2xl' => 3,
                            ])
                            ->schema([
                                TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->alphaDash()
                                    ->prefix('legal/'),

                                RichEditor::make('content')
                                    ->label('Contenu')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Configuration')
                            ->columnSpan([
                                'default' => 4,
                                '2xl' => 1,
                            ])
                            ->schema([
                                Toggle::make('is_footer')
                                    ->label('Afficher dans le footer')
                                    ->default(false),

                                Toggle::make('requires_acceptance')
                                    ->label('Requiert acceptation (Checkout)')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }
}

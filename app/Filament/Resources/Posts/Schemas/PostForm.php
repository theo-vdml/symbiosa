<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Shared\Actions\PublicationActions;
use App\Filament\Shared\Schemas\PublicationSchema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(4)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Contenu de l\'article')
                            ->columnSpan([
                                'default' => 4, // 4 colonnes sur mobile (tout s'empile)
                                '2xl' => 3,      // 1 colonne à partir des tablettes/laptops
                            ])

                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->label('Vignette')
                                    ->columnSpanFull()
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('posts/thumbnails')
                                    ->imageEditor()
                                    ->imagePreviewHeight(600)
                                    ->helperText('Image de couverture de l\'article. Recommandé : 1200x630px.'),

                                Select::make('category_id')
                                    ->label('Catégorie')
                                    ->relationship('category', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->quickAdd(label: "Nouvelle catégorie: {search}", resetSearch: true),

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
                                    ->prefix('https://symbiosa.be/news/')
                                    ->helperText('Généré automatiquement à partir du titre. Ne doit contenir que des tirets.'),

                                Textarea::make('excerpt')
                                    ->label('Extrait')
                                    ->autosize()
                                    ->required()
                                    ->helperText('Un court résumé de l\'article. Affiché sur la page d\'accueil et les listes d\'articles.'),

                                RichEditor::make('content')
                                    ->label('Contenu de l\'article')
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ]),
                            ]),

                        Section::make('Statut & Publication')
                            ->columnSpan([
                                'default' => 4, // 4 colonnes sur mobile (tout s'empile)
                                '2xl' => 1,      // 1 colonne à partir des tablettes/laptops
                            ])
                            ->schema([
                                ...PublicationSchema::make(),

                                Actions::make([
                                    ...PublicationActions::make("l'article"),
                                ])
                                    ->hidden(fn($record) => $record === null)
                                    ->verticalAlignment('start'),
                            ]),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('thumbnail')
                    ->columnSpanFull()
                    ->image()
                    ->directory('posts/thumbnails')
                    ->imageEditor()
                    ->imagePreviewHeight(600)
                    ->helperText('Image de couverture de l\'article. Recommandé : 1200x630px.'),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->columnSpanFull()
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique('categories', 'slug'),
                    ]),

                TextInput::make('title')
                    ->columnSpanFull()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->columnSpanFull()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->prefix('https://symbiosa.be/news/')
                    ->helperText('Généré automatiquement à partir du titre. Ne doit contenir que des tirets.'),

                RichEditor::make('content')
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
            ]);
    }
}

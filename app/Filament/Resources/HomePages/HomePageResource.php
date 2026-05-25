<?php

namespace App\Filament\Resources\HomePages;

use App\Enums\NavigationGroups;
use App\Filament\Resources\HomePages\Pages\CreateHomePage;
use App\Filament\Resources\HomePages\Pages\EditHomePage;
use App\Filament\Resources\HomePages\Pages\ListHomePages;
use App\Filament\Shared\Schemas\SeoSchema;
use App\Models\HomePage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class HomePageResource extends Resource
{
    protected static ?string $model = HomePage::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::Pages;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;
    protected static ?string $navigationLabel = "Homepage";
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hero')
                    ->description('Personnalisez l\'en-tête de la page d\'accueil.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextInput::make('hero_preheading')
                                        ->label('Pré-titre')
                                        ->placeholder('Ex: DJ Sets & Expériences'),
                                    TextInput::make('hero_title')
                                        ->label('Titre principal')
                                        ->placeholder('Ex: Symbiosa'),
                                    TextInput::make('hero_subheading')
                                        ->label('Sous-titre')
                                        ->placeholder('Ex: Belgique — Est. 2026'),
                                ]),
                                SpatieMediaLibraryFileUpload::make('hero_video')
                                    ->label('Vidéo de fond')
                                    ->collection('hero_video')
                                    ->helperText('Format recommandé : MP4 ou WebM. La première image sera utilisée comme poster.'),
                            ])
                    ]),

                Section::make('Bento Gallery')
                    ->columnSpanFull()
                    ->description('Gérez les images de la galerie en mosaïque. Ajoutez exactement 6 images pour afficher la galerie sur le site, ou 0 pour la masquer.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('bento_gallery')
                                    ->label('Images de la galerie')
                                    ->collection('bento_gallery')
                                    ->multiple()
                                    ->panelLayout('grid')
                                    ->appendFiles()
                                    ->reorderable()
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, $fail) {
                                                if (is_array($value) && count($value) > 0 && count($value) !== 6) {
                                                    $fail('La galerie doit contenir exactement 6 images (ou 0 pour être masquée).');
                                                }
                                            };
                                        },
                                    ])
                                    ->image()
                                    ->responsiveImages()
                                    ->optimize('webp'),

                                ViewEntry::make('bento_guide')
                                    ->view('filament.components.bento-layout-guide'),
                            ])
                    ]),

                Section::make('Playlist Spotify')
                    ->description('Gérez l\'intégration du lecteur Spotify sur l\'accueil.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextInput::make('spotify_playlist_heading')
                                        ->label('Titre affiché')
                                        ->placeholder('Ex: Nos coups de cœur du moment')
                                        ->prefixIcon(Heroicon::H1)
                                        ->maxLength(255),

                                    TextInput::make('spotify_playlist_id')
                                        ->label('Lien de la playlist')
                                        ->placeholder('https://open.spotify.com/playlist/...')
                                        ->prefixIcon(Heroicon::Link)
                                        ->hint('Collez l\'URL complète de la playlist.')
                                        ->formatStateUsing(fn($state) => $state ? "https://open.spotify.com/playlist/{$state}" : null)
                                        ->dehydrateStateUsing(function ($state) {
                                            if (empty($state)) return null;
                                            return Str::betweenFirst($state, 'playlist/', '?');
                                        })
                                        ->suffixAction(
                                            Action::make('open_playlist')
                                                ->icon(Heroicon::ArrowTopRightOnSquare)
                                                ->url(fn($state) => $state ?: null)
                                                ->visible(fn($state) => !empty($state)),
                                        )
                                        ->url()
                                        ->live(),
                                ]),

                                Section::make()
                                    ->contained(false)
                                    ->label('Options d\'affichage')
                                    ->schema([
                                        Toggle::make('show_spotify_playlist')
                                            ->label('Visibilité')
                                            ->helperText('Activer pour afficher ce bloc sur la page d\'accueil.')
                                            ->default(true),

                                        Toggle::make('spotify_playlist_force_dark')
                                            ->label('Forcer le thème sombre')
                                            ->helperText('Ajoute un paramètre pour forcer le lecteur Spotify en mode sombre.')
                                            ->default(false),
                                    ])
                            ]),
                    ]),

                SeoSchema::make()
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomePages::route('/'),
            'create' => CreateHomePage::route('/create'),
            'edit' => EditHomePage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return HomePage::count() === 0;
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if ($name === 'index' || $name === null) {
            $record = HomePage::first();
            if ($record) {
                return parent::getUrl('edit', ['record' => $record], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
            }
            return parent::getUrl('create', [], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}

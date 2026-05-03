<?php

namespace App\Filament\Pages;

use App\Settings\HomepageSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Str;
use UnitEnum;

class ManageHomepage extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::HomeModern;
    protected static ?string $navigationLabel = "Homepage";
    protected static string|UnitEnum|null $navigationGroup = "Pages";
    protected ?string $heading = "Homepage";
    protected ?string $subheading = "Modifier la page d'acceuil du site";


    protected static string $settings = HomepageSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Playlist Spotify')
                    ->description('Gérez l\'intégration du lecteur Spotify sur l\'accueil.')
                    ->columnSpanFull()
                    ->schema([
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

                        Toggle::make('show_spotify_playlist')
                            ->label('Visibilité')
                            ->helperText('Activer pour afficher ce bloc sur la page d\'accueil.')
                            ->default(true),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroups;
use App\Settings\AsblSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageAsblSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;
    protected static ?string $navigationLabel = "Informations ASBL";
    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::Admin;
    protected static ?int $navigationSort = 5;
    protected ?string $heading = "Informations ASBL";
    protected ?string $subheading = "Gérer les informations administratives de l'ASBL (Footer)";

    protected static string $settings = AsblSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détails de l\'organisation')
                    ->description('Ces informations seront affichées dans le bas de page (footer) du site.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Dénomination')
                            ->placeholder('Ex: Symbiosa ASBL')
                            ->required(),
                        Textarea::make('address')
                            ->label('Adresse')
                            ->placeholder('Ex: Rue de l\'Eglise 1, 1000 Bruxelles')
                            ->rows(3)
                            ->required(),
                        TextInput::make('vat')
                            ->label('Numéro de TVA')
                            ->placeholder('Ex: BE 0123.456.789'),
                    ]),
            ]);
    }
}

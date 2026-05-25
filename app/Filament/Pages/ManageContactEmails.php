<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroups;
use App\Settings\ContactSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageContactEmails extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAtSymbol;
    protected static ?string $navigationLabel = "Emails de contact";
    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::Admin;
    protected static ?int $navigationSort = 4;
    protected ?string $heading = "Emails de contact";
    protected ?string $subheading = "Gérer les adresses e-mail de contact pour l'ensemble du site";

    protected static string $settings = ContactSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Adresses e-mail de contact')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('email_options')
                            ->label('Emails')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Libellé')
                                    ->placeholder('Ex: Une question ?')
                                    ->prefixIcon(Heroicon::Tag)
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Adresse e-mail')
                                    ->placeholder('Ex: hi@symbiosa.be')
                                    ->prefixIcon(Heroicon::Envelope)
                                    ->email()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['label'] ?? null),
                    ]),
            ]);
    }
}
